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

class KupujemyModel extends Component\Model
{

	public function getDescription ()
	{
		return '<b>Kupujemy.pl</b> to serwis uruchomiony w 2003 roku, który przeszukuje polskie sklepy internetowe i pomaga w znalezieniu najlepszych ofert. Jesteśmy pierwszą w Polsce porównywarką cen.<br/>W chwili obecnej kupujemy.pl prezentuje oferty ponad 2000 sklepów internetowych, w tym prawie wszystkich oferujących sprzęt komputerowy, oprogramowanie, sprzęt fotograficzny, multimedia, telefony i akcesoria GSM, AGD i RTV.<br>';
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
					P.sellprice,
					PT.shortdescription,
					Photo.photoid,
					PT.seo,
					CT.name AS categoryname
				FROM product P
				LEFT JOIN producttranslation PT ON PT.productid = P.idproduct AND PT.languageid=:languageid
				LEFT JOIN productcategory PC ON PC.productid = P.idproduct
				LEFT JOIN categorytranslation CT ON PC.categoryid = CT.categoryid AND CT.languageid = :languageid
				LEFT JOIN productphoto Photo ON Photo.productid = P.idproduct AND Photo.mainphoto = 1
				WHERE P.enable = 1
	            GROUP BY P.idproduct";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->BindParam('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'categoryid' => $rs['id'],
				'seo' => $rs['seo'],
				'categoryname' => $rs['categoryname'],
				'productid' => $rs['idproduct'],
				'name' => $rs['name'],
				'shortdescription' => $rs['shortdescription'],
				'sellprice' => $rs['sellprice'],
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