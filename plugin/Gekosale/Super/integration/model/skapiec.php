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

class SkapiecModel extends Component\Model
{

	public function getDescription ()
	{
		return '<p><h3>Skąpiec.pl</h3></p>
<p>Skąpiec.pl jest serwisem propagującym zakupy w internecie. Potrafimy przy pomocy naszego oprogramowania przeglądać strony internetowe sklepów, tworzyć powiązania pomiędzy produktami z oferty i naszą bazą produktów oraz prezentować te wszystkie informacje naszym użytkownikom.
Skąpiec.pl nie jest sklepem i niczego nie sprzedaje. Naszym zadaniem jest jedynie pokazywanie w którym sklepie dany towar można nabyć najtaniej i który sprzedawca posiada najlepszą opinię
Skąpiec.pl odwiedza miesięcznie ok. 2 mln unikalnych użytkowników wykonując ponad 19 mln odsłon. (dane wg. Google Analitycs)</p>';
	}

	public function getConfigurationFields ()
	{
		return Array();
	}

	public function getProductListIntegration ()
	{
		$this->registry->template->assign('skapieccategories', $this->getCategories());
		
		$sql = "SELECT
					PC.categoryid AS id,
					P.idproduct,
					PT.name,
					(P.sellprice * (1 + (V.value / 100)) * CR.exchangerate) AS sellprice,
					IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * (1 + (V.value / 100)) * CR.exchangerate, NULL) AS discountprice,
					PT.shortdescription,
					Photo.photoid,
					PT.seo,
					PC.categoryid,
					PRT.name AS producername,
					P.weight
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
				'categoryid' => $rs['categoryid'],
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

	protected function getCategories ()
	{
		$sql = '
				SELECT
					C.idcategory AS id,
					CT.name AS label
				FROM
					category C
					INNER JOIN viewcategory CV ON CV.categoryid = idcategory
					LEFT JOIN categorytranslation CT ON C.idcategory = CT.categoryid AND CT.languageid = :languageid
				WHERE
					CV.viewid = :viewid AND C.enable = 1
				GROUP BY
					C.idcategory
				ORDER BY
					C.distinction ASC
			';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->BindParam('languageid', Helper::getLanguageId());
		$stmt->BindParam('viewid', Helper::getViewId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'catid' => $rs['id'],
				'catname' => $rs['label']
			);
		}
		return $Data;
	}
}