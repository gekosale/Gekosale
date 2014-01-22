<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2011 Gekosale. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 309 $
 * $Author: gekosale $
 * $Date: 2011-08-01 21:10:16 +0200 (Pn, 01 sie 2011) $
 * $Id: product.php 309 2011-08-01 19:10:16Z gekosale $
 */
namespace Gekosale\Plugin;

class CategoriesModel extends Component\Model
{
	protected $_rawData;

	public function getCategory ($id)
	{
		$sql = 'SELECT
					C.idcategory AS id,
					C.categoryid AS catid,
					C.photoid,
					C.distinction,
					C.enable
				FROM category C
				WHERE C.idcategory=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'id' => $rs['id'],
				'parentid' => $rs['catid'],
				'distinction' => $rs['distinction'],
				'enable' => $rs['enable'],
				'photos' => App::getModel('webapi')->getPhotos($rs['photoid']),
				'translation' => $this->getCategoryTranslation($id)
			);
			return $Data;
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function getCategories ()
	{
		$sql = 'SELECT
					C.idcategory AS id,
					C.categoryid AS catid,
					C.photoid,
					C.distinction,
					C.enable
				FROM category C
				GROUP BY C.idcategory';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'parentid' => $rs['catid'],
				'distinction' => $rs['distinction'],
				'enable' => $rs['enable'],
				'photos' => App::getModel('webapi')->getPhotos($rs['photoid']),
				'translation' => $this->getCategoryTranslation($rs['id'])
			);
		}
		return $Data;
	}

	public function getCategoryTranslation ($id)
	{
		$sql = "SELECT
					name,
					shortdescription,
					description,
					seo,
					languageid,
					keyword_title,
					keyword,
					keyword_description
				FROM categorytranslation
				WHERE categoryid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['languageid']] = Array(
				'name' => $rs['name'],
				'shortdescription' => $rs['shortdescription'],
				'description' => $rs['description'],
				'link' => $this->registry->router->generate('frontend.categorylist', true, Array(
					'param' => $rs['seo']
				)),
				'seo' => $rs['seo'],
				'keywordtitle' => $rs['keyword_title'],
				'keyword' => $rs['keyword'],
				'keyworddescription' => $rs['keyword_description']
			);
		}
		
		return $Data;
	}

	public function getCategoriesTree ()
	{
		$this->_loadRawCategoriesFromDatabase();
		return $this->_parseCategorySubtree();
	}

	protected function _loadRawCategoriesFromDatabase ()
	{
		$sql = '
				SELECT
					C.idcategory AS id,
					C.categoryid AS parent
				FROM
					category C
				GROUP BY id
			';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$this->_rawData = $stmt->fetchAll();
	}

	protected function _parseCategorySubtree ($parent = null)
	{
		$categories = Array();
		foreach ($this->_rawData as $category){
			if ($parent == null){
				if ($category['parent'] != ''){
					continue;
				}
			}
			elseif ($category['parent'] != $parent){
				continue;
			}
			$categories[] = Array(
				'id' => $category['id'],
				'children' => $this->_parseCategorySubtree($category['id'])
			);
		}
		return $categories;
	}

	public function addCategory ($Data)
	{
		$sql = 'INSERT INTO category SET
					categoryid = :categoryid,
					distinction = :distinction,
					enable = :enable';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('categoryid', ((int) $Data['parentid'] > 0) ? $Data['parentid'] : NULL);
		$stmt->bindValue('enable', isset($Data['enable']) ? (int) $Data['enable'] : 0);
		$stmt->bindValue('distinction', $Data['distinction']);
		$stmt->execute();
		
		$categoryid = Db::getInstance()->lastInsertId();
		
		foreach ($Data['translation'] as $key => $val){
			$sql = 'INSERT INTO categorytranslation (categoryid,name,shortdescription, description, languageid, seo, keyword_title, keyword, keyword_description)
					VALUES (:categoryid,:name,:shortdescription, :description, :languageid, :seo, :keyword_title, :keyword, :keyword_description)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('categoryid', $categoryid);
			$stmt->bindValue('name', $val['name']);
			$stmt->bindValue('shortdescription', $val['shortdescription']);
			$stmt->bindValue('description', $val['description']);
			$stmt->bindValue('languageid', $key);
			$stmt->bindValue('seo', $val['seo']);
			$stmt->bindValue('keyword_title', $val['keywordtitle']);
			$stmt->bindValue('keyword', $val['keyword']);
			$stmt->bindValue('keyword_description', $val['keyworddescription']);
			$stmt->execute();
		}
		
		$sql = 'INSERT INTO viewcategory (categoryid,viewid)
				VALUES (:categoryid, :viewid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('categoryid', $categoryid);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		
		$this->flushCache();
		
		return Array(
			'success' => true,
			'id' => $categoryid
		);
	}

	public function updateCategory ($Data)
	{
		$sql = 'UPDATE category SET
					categoryid = :categoryid,
					distinction = :distinction,
					enable = :enable
				WHERE idcategory = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $Data['id']);
		$stmt->bindValue('categoryid', ((int) $Data['parentid'] > 0) ? $Data['parentid'] : NULL);
		$stmt->bindValue('enable', isset($Data['enable']) ? (int) $Data['enable'] : 0);
		$stmt->bindValue('distinction', $Data['distinction']);
		$stmt->execute();
		
		DbTracker::deleteRows('categorytranslation', 'categoryid', $Data['id']);
		
		foreach ($Data['translation'] as $key => $val){
			$sql = 'INSERT INTO categorytranslation (categoryid,name,shortdescription, description, languageid, seo, keyword_title, keyword, keyword_description)
					VALUES (:categoryid,:name,:shortdescription, :description, :languageid, :seo, :keyword_title, :keyword, :keyword_description)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('categoryid', $Data['id']);
			$stmt->bindValue('name', $val['name']);
			$stmt->bindValue('shortdescription', $val['shortdescription']);
			$stmt->bindValue('description', $val['description']);
			$stmt->bindValue('languageid', $key);
			$stmt->bindValue('seo', $val['seo']);
			$stmt->bindValue('keyword_title', $val['keywordtitle']);
			$stmt->bindValue('keyword', $val['keyword']);
			$stmt->bindValue('keyword_description', $val['keyworddescription']);
			$stmt->execute();
		}
		
		$this->flushCache();
		
		return Array(
			'success' => true
		);
	}

	public function deleteCategory ($id)
	{
		DbTracker::deleteRows('category', 'idcategory', $id);
		
		$this->flushCache();
		
		return Array(
			'success' => true
		);
	}

	public function doAJAXCreateSeoCategory ($request)
	{
		$name = trim($request['name']);
		
		$sql = 'SELECT
					GROUP_CONCAT(SUBSTRING(IF(CT.categoryid = :id, :name, LOWER(CT.name)), 1) ORDER BY C.order DESC SEPARATOR \'/\') AS seo
				FROM categorytranslation CT
				LEFT JOIN categorypath C ON C.ancestorcategoryid = CT.categoryid
				WHERE C.categoryid = :id AND CT.languageid = :languageid';
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

	public function flushCache ()
	{
		App::getContainer()->get('cache')->delete('categories');
		App::getContainer()->get('cache')->delete('contentcategory');
		App::getContainer()->get('cache')->delete('sitemapcategories');
	}
} 