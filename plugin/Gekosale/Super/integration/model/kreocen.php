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

class KreocenModel extends Component\Model
{

	public function getDescription ()
	{
		return '<font style="font-size:17px; color:#F90; font-family:Georgia; font-style:italic;">KreoCEN</font> <font style="font-size:17px; color:#575757; font-family:Georgia; font-style:italic;">to innowacyjna, w pełni profesjonalna i kompletna porównywarka cen materiałów budowlanych i wykończeniowych.</font><br><br>
Misją <font style="font-size:13px; font-weight:bold; color:#F90;">KreoCEN</font> jest pomoc osobom budującym oraz aranżującym swoje domy i mieszkania w łatwym odnalezieniu najtańszych produktów na rynku.<br><br>
Realizując tę misję, dbamy o to, aby <font style="font-size:13px; font-weight:bold; color:#F90;">KreoCEN</font> był łatwy w użytkowaniu, przejrzysty oraz w możliwie doskonały sposób selekcjonował i wyszukiwał tylko te produktu, które są potrzebne użytkownikowi. Szczególny nacisk kładziemy na to, by prezentowane przez nas dane były rzetelne, aktualne i właściwie dobrane.<br>
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
					WHERE C.categoryid = PC.categoryid AND CT.languageid = :languageid) AS kreocen
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
		$Data = Array();
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
				'kreocen' => $rs['kreocen']
			);
		}
		foreach ($Data as $key => $Product){
			$Image = App::getModel('gallery')->getOrginalImageById($Product['photoid']);
			$Data[$key]['photo'] = App::getModel('gallery')->getImagePath($Image, App::getURLAdress());
		}
		return $Data;
	}
}