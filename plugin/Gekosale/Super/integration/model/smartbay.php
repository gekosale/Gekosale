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

class SmartbayModel extends Component\Model
{

	public function getDescription ()
	{
		return '<p><h3>SmartBay.pl</h3> umożliwia sklepom realne korzyści wynikacjące z prezentacji swojej oferty w naszym serwisie. Oferty sklepów są dostępne dla szerokiego grona Użytkowników, którzy odwiedzają nasz serwis z zamiarem zrobienia zakupów w sieci. Wymiana opinii o sklepach w SmartBay.pl oraz poprzez serwisy partnerskie umożliwia budowanie marki i zaufania wśród kupujących.</p>';
	}

	public function getConfigurationFields ()
	{
		return Array();
	}
	protected $num;

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
					WHERE C.categoryid = PC.categoryid AND CT.languageid = :languageid) AS smartbay
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
		$stmt->BindParam('languageid', Helper::getLanguageId());
		$stmt->BindParam('currencyto', App::getContainer()->get('session')->getActiveCurrencyId());
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
				'smartbay' => $rs['smartbay']
			);
		}
		foreach ($Data as $key => $Product){
			$Image = App::getModel('gallery')->getOrginalImageById($Product['photoid']);
			$Data[$key]['photo'] = App::getModel('gallery')->getImagePath($Image, App::getURLAdress());
		}
		
		$this->registry->template->assign('smartbaynumberofproducts', count($Data));
		return $Data;
	}

	public function getNumberOfCategories ()
	{
		return $this->num;
	}
}