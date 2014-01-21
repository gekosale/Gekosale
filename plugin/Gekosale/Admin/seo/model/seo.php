<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 612 $
 * $Author: gekosale $
 * $Date: 2011-11-28 21:02:10 +0100 (Pn, 28 lis 2011) $
 * $Id: seo.php 612 2011-11-28 20:02:10Z gekosale $ 
 */
namespace Gekosale;

use xajaxResponse;

class SeoModel extends Component\Model
{

	public function doAJAXCreateSeo ($request)
	{
		$seo = Core::clearUTF(trim($request['name']));
		$seo = preg_replace('/[^A-Za-z0-9\-\s\s+]/', '', $seo);
		$seo = Core::clearSeoUTF($seo);
		
		return Array(
			'seo' => str_replace('/', '', strtolower($seo))
		);
	}

	public function clearSeoUTF ($name)
	{
		$seo = Core::clearUTF(trim($name));
		$seo = preg_replace('/[^A-Za-z0-9\-\s\s+]/', '', $seo);
		$seo = Core::clearSeoUTF($seo);
		return str_replace('/', '', strtolower($seo));
	}

	public function doAJAXRefreshSeoProducts ()
	{
		@set_time_limit(0);
		Db::getInstance()->beginTransaction();
		$sql = 'SELECT
					PT.productid AS id,
					PT.name AS name
				FROM producttranslation PT
				WHERE PT.languageid = :languageid
				';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$seo = $this->doAJAXCreateSeo(Array(
				'name' => $rs['name']
			));
			
			$sql = 'UPDATE producttranslation SET
						seo = :seo
					WHERE languageid = :languageid AND productid = :id
			';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('seo', $seo['seo']);
			$stmt->bindValue('languageid', Helper::getLanguageId());
			$stmt->bindValue('id', $rs['id']);
			$stmt->execute();
		}
		
		Db::getInstance()->commit();
	}

	public function doAJAXCreateSeoCategory ($request)
	{
		$name = trim($request['name']);
		
		$sql = 'SELECT
					GROUP_CONCAT(SUBSTRING(IF(CT.categoryid = :id, :name, LOWER(CT.name)), 1) ORDER BY C.order DESC SEPARATOR \'/\') AS seo
				FROM categorytranslation CT
				LEFT JOIN categorypath C ON C.ancestorcategoryid = CT.categoryid
				WHERE C.categoryid = :id AND CT.languageid = :languageid
				';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $this->registry->core->getParam());
		$stmt->bindValue('languageid', $request['language']);
		$stmt->bindValue('name', $name);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			if (! is_null($rs['seo'])){
				$seo = Core::clearSeoUTF($rs['seo']);
			}
			else{
				$seo = Core::clearSeoUTF($name);
			}
		}
		else{
			$seo = Core::clearSeoUTF($name);
		}
		
		return Array(
			'seo' => strtolower($seo)
		);
	}

	public function doRefreshSeoCategory ()
	{
		Db::getInstance()->beginTransaction();
		$sql = 'SELECT idcategory FROM category';
		$stmt = $stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$sql2 = 'SELECT
						CT.languageid,
						GROUP_CONCAT(SUBSTRING(IF(CT.categoryid = :id, CT.name, LOWER(CT.name)), 1) ORDER BY C.order DESC SEPARATOR \'/\') AS seo
					FROM categorytranslation CT
					LEFT JOIN categorypath C ON C.ancestorcategoryid = CT.categoryid
					WHERE C.categoryid = :id 
					GROUP BY C.categoryid, CT.languageid
					';
			$stmt2 = Db::getInstance()->prepare($sql2);
			$stmt2->bindValue('id', $rs['idcategory']);
			$stmt2->execute();
			$rs2 = $stmt2->fetch();
			if ($rs2){
				
				$seo = Core::clearSeoUTF($rs2['seo']);
				
				$sql3 = 'UPDATE categorytranslation SET
							seo = :seo
						WHERE
							categoryid = :categoryid AND languageid = :languageid
						';
				
				$stmt3 = Db::getInstance()->prepare($sql3);
				$stmt3->bindValue('categoryid', $rs['idcategory']);
				$stmt3->bindValue('languageid', $rs2['languageid']);
				$stmt3->bindValue('seo', strtolower($seo));
				$stmt3->execute();
			}
		}
		
		Db::getInstance()->commit();
		App::getModel('category')->flushCache();
	}

	public function doAJAXRefreshSeoCategory ()
	{
		$objResponse = new xajaxResponse();
		$this->doRefreshSeoCategory();
		$objResponse->script('window.location.reload(false)');
		return $objResponse;
	}

	public function getMetadataForPage ()
	{
		$controller = $this->registry->router->getCurrentController();
		$Data = Array();
		$sql = "SELECT
					VT.keyword_title,
					C.description,
					VT.keyword,
					VT.keyword_description
				FROM controller C
				LEFT JOIN viewtranslation VT ON VT.viewid = :viewid
				WHERE C.name = :controller AND C.mode = 0";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('controller', $controller);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			if ($rs['keyword_title'] == NULL || $rs['keyword_title'] == ''){
				$keyword_title = ($controller == 'mainside') ? App::getContainer()->get('session')->getActiveShopName() : $this->trans($rs['description']);
			}
			else{
				$keyword_title = $rs['keyword_title'];
			}
			$title = ($controller == 'mainside') ? $keyword_title : $this->trans($rs['description']);
			$Data = Array(
				'keyword_title' => $title,
				'keyword' => $rs['keyword'],
				'keyword_description' => $rs['keyword_description']
			);
		}
		return $Data;
	}
}