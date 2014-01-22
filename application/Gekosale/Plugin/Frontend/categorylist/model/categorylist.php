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
 * $Revision: 627 $
 * $Author: gekosale $
 * $Date: 2012-01-20 23:05:57 +0100 (Pt, 20 sty 2012) $
 * $Id: categorylist.php 627 2012-01-20 22:05:57Z gekosale $
 */
namespace Gekosale\Plugin;

class CategoryListModel extends Component\Model
{
	protected $_currentCategory = Array();

	public function getCategoryNameTop ($id)
	{
		$sql = "SELECT
					name AS parentname,
					seo
				FROM categorytranslation
				WHERE categoryid =:id AND languageid = :languageid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'id' => $id,
				'parentname' => $rs['parentname'],
				'seo' => $rs['seo']
			);
		}
		return $Data;
	}

	public function getImagePath ($id)
	{
		if ($id > 0){
			return App::getModel('gallery')->getImagePath(App::getModel('gallery')->getOrginalImageById($id, 0));
		}
	}

	public function getCategoryMenuTop ($id)
	{
		$sql = "SELECT 
					C.idcategory, 
					CT.name,
					CT.seo,
					C.photoid,
					CT.shortdescription,
					CT.description,
     				(SELECT count(productid) FROM productcategory WHERE categoryid = idcategory) AS qry
				FROM category C
				LEFT JOIN viewcategory VC ON VC.categoryid = C.idcategory
				LEFT JOIN categorytranslation CT ON CT.categoryid = idcategory AND CT.languageid = :languageid
				WHERE C.categoryid=:id AND VC.viewid=:viewid AND C.enable = 1
				GROUP BY C.idcategory
				ORDER BY C.distinction ASC";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'name' => $rs['name'],
				'idcategory' => $rs['idcategory'],
				'qry' => $rs['qry'],
				'seo' => $rs['seo'],
				'shortdescription' => $rs['shortdescription'],
				'description' => $rs['description'],
				'photo' => $this->getImagePath($rs['photoid'])
			);
		}
		return $Data;
	}

	public function getMetadataForCategory ()
	{
		return $this->getCurrentCategory();
	}

	public function getCategoryBySeo ($seo)
	{
		$sql = "SELECT
					CT.categoryid,
					CT.name,
					CT.seo,
					CT.shortdescription,
					CT.description,
					C.photoid,
					C.categoryid AS parent,
					IF(CT.keyword_title IS NULL OR CT.keyword_title = '', CT.name, CT.keyword_title)  AS keyword_title, 
					IF(CT.keyword = '',VT.keyword, CT.keyword) AS keyword, 
					IF(CT.keyword_description = '', VT.keyword_description,CT.keyword_description) AS keyword_description
				FROM categorytranslation CT
				LEFT JOIN category C ON CT.categoryid = C.idcategory
				LEFT JOIN viewcategory VC ON CT.categoryid = VC.categoryid 
				LEFT JOIN viewtranslation VT ON VT.viewid = VC.viewid
				WHERE CT.seo =:seo AND CT.languageid = :languageid AND VC.viewid = :viewid AND C.enable = 1";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->bindValue('seo', $seo);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		$Data = Array();
		if ($rs){
			$this->_currentCategory = Array(
				'id' => $rs['categoryid'],
				'parent' => $rs['parent'],
				'name' => $rs['name'],
				'seo' => $rs['seo'],
				'shortdescription' => $rs['shortdescription'],
				'description' => $rs['description'],
				'keyword_title' => $rs['keyword_title'],
				'keyword' => $rs['keyword'],
				'keyword_description' => $rs['keyword_description'],
				'photo' => $this->getImagePath($rs['photoid'])
			);
		}
	}

	public function getCategoryById ($id)
	{
		$sql = "SELECT
					CT.categoryid,
					CT.name,
					CT.seo,
					CT.shortdescription,
					CT.description,
					C.photoid,
					C.categoryid AS parent
				FROM categorytranslation CT
				LEFT JOIN category C ON CT.categoryid = C.idcategory
				LEFT JOIN viewcategory VC ON CT.categoryid = VC.categoryid 
				WHERE C.idcategory =:id AND CT.languageid = :languageid AND VC.viewid = :viewid AND C.enable = 1";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		$Data = Array();
		if ($rs){
			$Data = Array(
				'id' => $rs['categoryid'],
				'name' => $rs['name'],
				'seo' => $rs['seo'],
				'shortdescription' => $rs['shortdescription'],
				'description' => $rs['description'],
				'photo' => $this->getImagePath($rs['photoid'])
			);
		}
		return $Data;
	}

	public function getCurrentCategory ()
	{
		if (strlen($this->getParam()) == 0){
			App::redirectUrl($this->registry->router->generate('frontend.sitemap', true));
		}
		if (empty($this->_currentCategory)){
			$this->getCategoryBySeo($this->getParam());
		}
		return $this->_currentCategory;
	}
}