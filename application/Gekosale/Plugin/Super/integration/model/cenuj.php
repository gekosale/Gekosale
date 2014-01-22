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

class CenujModel extends Component\Model
{

	public function getDescription ()
	{
		return '<p><h3><b>Cenuj.pl</b> to porównywarka cen i wyszukiwarka produktów oraz usług dostępnych w polskim e-biznesie.</h3></p>
<p>Umożliwia i pomaga w łatwym i szybkim odnalezieniu szukanego produktu lub usługi porównując ceny w polskich sklepach internetowych. Dzięki współpracy właścicieli sklepów masz możliwość odnalezienia tego produktu, który tak naprawdę Cię interesuje. Porównywarka jest ciągle aktualizowana oraz kontrolowana przez administratorów serwisu.</p>
';
	}

	public function getConfigurationFields ()
	{
		return array();
	}

	public function getProductListIntegration ()
	{
		$sql = "SELECT
					P.idproduct,
					PT.name,
					(P.sellprice * (1 + (V.value / 100)) * CR.exchangerate) AS sellprice,
					IF(P.promotion = 1 AND IF(P.promotionstart IS NOT NULL, P.promotionstart <= CURDATE(), 1) AND IF(P.promotionend IS NOT NULL, P.promotionend >= CURDATE(), 1), P.discountprice * (1 + (V.value / 100)) * CR.exchangerate, NULL) AS discountprice,
					PT.shortdescription,
					Photo.photoid,
					PRT.name AS producername,
					PT.seo,
					(SELECT
					    GROUP_CONCAT(SUBSTRING(CT.name, 1) ORDER BY C.order DESC SEPARATOR ' / ')
					FROM categorytranslation CT
					LEFT JOIN categorypath C ON C.ancestorcategoryid = CT.categoryid
					WHERE C.categoryid = PC.categoryid AND CT.languageid = :languageid) AS cenuj
				FROM product P
				LEFT JOIN producttranslation PT ON PT.productid = P.idproduct AND PT.languageid=:languageid
				LEFT JOIN productcategory PC ON PC.productid = P.idproduct
				LEFT JOIN categorypath CP ON CP.ancestorcategoryid = PC.categoryid
				LEFT JOIN categorytranslation CT ON CP.ancestorcategoryid = CT.categoryid AND CT.languageid = :languageid
				LEFT JOIN productphoto Photo ON Photo.productid = P.idproduct AND Photo.mainphoto=1
				LEFT JOIN producertranslation PRT ON PRT.producerid = P.producerid AND PRT.languageid = :languageid
				LEFT JOIN vat V ON P.vatid= V.idvat
				LEFT JOIN currencyrates CR ON CR.currencyfrom = P.sellcurrencyid AND CR.currencyto = :currencyto
				WHERE P.enable = 1
	            GROUP BY P.idproduct";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindParam('languageid', Helper::getLanguageId());
		$stmt->bindParam('currencyto', App::getContainer()->get('session')->getActiveCurrencyId());
		$stmt->execute();
		$Data = array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'productid' => $rs['idproduct'],
				'seo' => $rs['seo'],
				'name' => $rs['name'],
				'shortdescription' => $rs['shortdescription'],
				'sellprice' => number_format((! is_null($rs['discountprice'])) ? $rs['discountprice'] : $rs['sellprice'], 2),
				'photoid' => $rs['photoid'],
				'idproduct' => $rs['idproduct'],
				'producername' => $rs['producername'],
				'cenuj' => $rs['cenuj']
			);
		}
		foreach ($Data as $key => $Product){
			$Image = App::getModel('gallery')->getOrginalImageById($Product['photoid']);
			$Data[$key]['photo'] = App::getModel('gallery')->getImagePath($Image, App::getURLAdress());
		}
		return $Data;
	}
}