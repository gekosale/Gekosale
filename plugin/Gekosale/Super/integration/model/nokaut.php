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
namespace Gekosale;

class NokautModel extends Component\Model
{

	public function getDescription ()
	{
		return '<b>Nokaut.pl</b> wszystkim sklepom gwarantuje realne i wymierne korzyści płynące z prezentacji swojej oferty w serwisie. Poprzez obecność w Nokaut.pl oferty sklepów są stale dostępne dla użytkowników aktywnie dokonujących zakupów w Internecie. Poprzez płynną wymianę opinii pomiędzy użytkownikami, Nokaut.pl promuje marki sklepów internetowych.';
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
					P.ean,
					PT.name,
					(P.sellprice * (1 + (V.value / 100)) * CR.exchangerate) AS sellprice,
					IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * (1 + (V.value / 100)) * CR.exchangerate, NULL) AS discountprice,
					PT.shortdescription,
					Photo.photoid,
					PT.seo,
					AT.name AS availablity,
					(SELECT
					    GROUP_CONCAT(SUBSTRING(CT.name, 1) ORDER BY C.order DESC SEPARATOR ' / ')
					FROM categorytranslation CT
					LEFT JOIN categorypath C ON C.ancestorcategoryid = CT.categoryid
					WHERE C.categoryid = PC.categoryid AND CT.languageid = :languageid) AS nokaut,
					PRT.name AS producername,
					P.weight,
					IF(P.trackstock = 1, P.stock, 1) AS stock
				FROM product P
				LEFT JOIN vat V ON P.vatid= V.idvat
				LEFT JOIN producttranslation PT ON PT.productid = P.idproduct AND PT.languageid=:languageid
				LEFT JOIN productcategory PC ON PC.productid = P.idproduct
				INNER JOIN viewcategory VC ON VC.categoryid = PC.categoryid AND VC.viewid = :viewid
				LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
				LEFT JOIN categorypath CP ON CP.ancestorcategoryid = PC.categoryid
				LEFT JOIN categorytranslation CT ON CP.ancestorcategoryid = CT.categoryid AND CT.languageid = :languageid
				LEFT JOIN producertranslation PRT ON PRT.producerid = P.producerid AND PRT.languageid = :languageid
				LEFT JOIN productphoto Photo ON Photo.productid = P.idproduct AND Photo.mainphoto = 1
				LEFT JOIN availablitytranslation AT ON P.availablityid = AT.availablityid AND AT.languageid = :languageid
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
				'ean' => $rs['ean'],
				'categoryname' => $rs['nokaut'],
				'producername' => $rs['producername'],
				'productid' => $rs['idproduct'],
				'name' => $rs['name'],
				'shortdescription' => $rs['shortdescription'],
				'sellprice' => number_format((! is_null($rs['discountprice'])) ? $rs['discountprice'] : $rs['sellprice'], 2, '.', ''),
				'photoid' => $rs['photoid'],
				'idproduct' => $rs['idproduct'],
				'avail' => ($rs['stock'] > 0) ? 0 : 4,
				'stock' => $rs['stock'],
			);
		}
		foreach ($Data as $key => $Product){
			$Image = App::getModel('gallery')->getOrginalImageById($Product['photoid']);
			$Data[$key]['photo'] = App::getModel('gallery')->getImagePath($Image, App::getURLAdress());
		}
		return $Data;
	}
}