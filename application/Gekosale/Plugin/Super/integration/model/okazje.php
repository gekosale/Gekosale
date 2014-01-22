<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace Gekosale\Plugin;

class OkazjeModel extends Component\Model
{

	public function getDescription ()
	{
		return '<b>Okazje.info</b> to sposób na tanie i udane zakupy. Znajdziesz tu porównanie cen w sklepach internetowych i tradycyjnych oraz aktualne promocje. Dowiesz się co i gdzie kupić najtaniej. Do tego: porady, recenzje i opinie o produktach. Nie trać czasu! Porównaj ceny i przejrzyj oferty sklepów internetowych – przekonasz się, że warto.';
	}

	public function getConfigurationFields ()
	{
		return Array();
	}

	public function getProductListIntegration ()
	{
		$sql = "SELECT
					PC.categoryid AS id,
					P.idproduct,
					PT.name,
					IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * (1 + (V.value / 100)) * CR.exchangerate, NULL) AS discountprice,
					(P.sellprice * (1 + (V.value / 100)) * CR.exchangerate) AS sellprice,
					PT.shortdescription,
					Photo.photoid,
					PT.seo,
					CT.name AS categoryname,
					PRT.name AS producername
				FROM product P
				LEFT JOIN producttranslation PT ON PT.productid = P.idproduct AND PT.languageid=:languageid
				LEFT JOIN productcategory PC ON PC.productid = P.idproduct
				INNER JOIN viewcategory VC ON VC.categoryid = PC.categoryid AND VC.viewid = :viewid
				LEFT JOIN categorytranslation CT ON PC.categoryid = CT.categoryid AND CT.languageid = :languageid
				LEFT JOIN producertranslation PRT ON PRT.producerid = P.producerid AND PRT.languageid = :languageid
				LEFT JOIN productphoto Photo ON Photo.productid = P.idproduct AND Photo.mainphoto = 1
				LEFT JOIN vat V ON P.vatid= V.idvat
				LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
				WHERE P.enable = 1
	            GROUP BY P.idproduct";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('currencyto', App::getContainer()->get('session')->getActiveCurrencyId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'categoryid' => $rs['id'],
				'seo' => $rs['seo'],
				'categoryname' => $rs['categoryname'],
				'producername' => $rs['producername'],
				'productid' => $rs['idproduct'],
				'name' => $rs['name'],
				'shortdescription' => $rs['shortdescription'],
				'sellprice' => number_format((! is_null($rs['discountprice'])) ? $rs['discountprice'] : $rs['sellprice'], 2, '.', ''),
				'photoid' => $rs['photoid'],
				'idproduct' => $rs['idproduct']
			);
		}
		foreach ($Data as $key => $Product){
			$Image = App::getModel('gallery')->getOrginalImageById($Product['photoid']);
			$Data[$key]['photo'] = App::getModel('gallery')->getImagePath($Image, App::getURLAdress());
		}
		return $Data;
	}
}