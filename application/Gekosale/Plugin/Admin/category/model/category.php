<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o
 * licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 602 $
 * $Author: gekosale $
 * $Date: 2011-11-07 22:45:33 +0100 (Pn, 07 lis 2011) $
 * $Id: category.php 602 2011-11-07 21:45:33Z gekosale $
 */
namespace Gekosale\Plugin;

use xajaxResponse;

class categoryModel extends Component\Model
{

	public function doAJAXDeleteCategory ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteCategory'
		), $this->getName());
	}

	public function deleteCategory ($id)
	{
		DbTracker::deleteRows('category', 'idcategory', $id);
	}

	public function getCategoryView ($id)
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
				'catid' => $rs['catid'],
				'photoid' => $rs['photoid'],
				'distinction' => $rs['distinction'],
				'enable' => $rs['enable'],
				'language' => $this->getCategoryTranslation($id),
				'view' => $this->getCategoryViews($id),
				'next' => $this->getNextCategoryId($rs['id'], $rs['catid'], $rs['distinction'])
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

	public function getNextCategoryId ($id, $parent, $distinction)
	{
		if ($parent == NULL){
			$sql = 'SELECT
						C.idcategory AS id
					FROM category C
					WHERE C.idcategory !=:id AND C.categoryid IS NULL AND C.distinction > :distinction
					ORDER BY C.distinction ASC
					LIMIT 1';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $id);
			$stmt->bindValue('distinction', $distinction);
			$stmt->execute();
			$rs = $stmt->fetch();
		}
		else{
			$sql = 'SELECT
						C.idcategory AS id
					FROM category C
					WHERE C.idcategory !=:id AND C.categoryid = :parent AND C.distinction > :distinction
					ORDER BY C.distinction ASC
					LIMIT 1';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $id);
			$stmt->bindValue('parent', $parent);
			$stmt->bindValue('distinction', $distinction);
			$stmt->execute();
			$rs = $stmt->fetch();
		}
		if ($rs){
			return $rs['id'];
		}
		else{
			return 0;
		}
	}

	public function getCategoryViews ($id)
	{
		$sql = "SELECT
					viewid
				FROM viewcategory
				WHERE categoryid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['viewid'];
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
				'seo' => $rs['seo'],
				'keywordtitle' => $rs['keyword_title'],
				'keyword' => $rs['keyword'],
				'keyworddescription' => $rs['keyword_description']
			);
		}
		
		return $Data;
	}

	public function addEmptyCategory ($request)
	{
		$data = Array(
			'categoryid' => isset($request['parent']) ? $request['parent'] : null,
			'shortdescription' => null,
			'description' => null,
			'discount' => null,
			'photo' => null,
			'name' => (isset($request['name']) && strlen($request['name'])) ? $request['name'] : $this->trans('TXT_NEW_CATEGORY')
		);
		return Array(
			'id' => $this->addCategory($data)
		);
	}

	public function changeCategoryOrder ($request)
	{
		if (! isset($request['items']) || ! is_array($request['items'])){
			throw new Exception('No data received.');
		}
		$sql = '
				UPDATE
					category
				SET
					categoryid = :categoryid,
					distinction = :distinction
				WHERE
					idcategory = :id
			';
		foreach ($request['items'] as $item){
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $item['id']);
			$stmt->bindValue('distinction', $item['weight']);
			if (! isset($item['parent']) || empty($item['parent'])){
				$stmt->bindValue('categoryid', NULL);
			}
			else{
				$stmt->bindValue('categoryid', $item['parent']);
			}
			$stmt->execute();
		}
		// $this->getCategoriesPathById();
		$this->flushCache();
		return Array(
			'status' => $this->trans('TXT_CATEGORY_ORDER_SAVED')
		);
	}

	protected function checkDuplicateNames ($name, $seo, $languageid, $counter)
	{
		$valid = false;
		
		while (true){
			$counter ++;
			
			$sql = "SELECT
						name,
						seo
					FROM categorytranslation
					WHERE languageid = :languageid AND name = :name AND seo = :seo";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('languageid', $languageid);
			$stmt->bindValue('name', $name . ' (' . $counter . ')');
			$stmt->bindValue('seo', $seo . '-' . $counter);
			$stmt->execute();
			$rs = $stmt->fetch();
			if (! $rs){
				return Array(
					'name' => $name . ' (' . $counter . ')',
					'seo' => $seo . '-' . $counter
				);
				break;
			}
		}
	}

	public function duplicateCategory ($id)
	{
		$objResponse = new xajaxResponse();
		
		Db::getInstance()->beginTransaction();
		$Data = $this->getCategoryView($id);
		
		$sql = 'INSERT INTO category (categoryid,distinction,enable, photoid)
				VALUES (:categoryid,:distinction,:enable, :photoid)';
		$stmt = Db::getInstance()->prepare($sql);
		if ($Data['catid'] != 0){
			$stmt->bindValue('categoryid', $Data['catid']);
		}
		else{
			$stmt->bindValue('categoryid', NULL);
		}
		$stmt->bindValue('enable', 0);
		if (isset($Data['photoid']) && $Data['photoid'] > 0){
			$stmt->bindValue('photoid', $Data['photoid']);
		}
		else{
			$stmt->bindValue('photoid', NULL);
		}
		$stmt->bindValue('distinction', $Data['distinction']);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CATEGORY_ADD'), 3003, $e->getMessage());
		}
		
		$categoryid = Db::getInstance()->lastInsertId();
		
		foreach ($Data['language'] as $key => $val){
			
			$names = $this->checkDuplicateNames($val['name'], $val['seo'], $key, 0);
			
			$sql = 'INSERT INTO categorytranslation (
						categoryid,
						name,
						shortdescription,
						description,
						languageid,
						seo,
						keyword_title,
						keyword,
						keyword_description
					)
					VALUES
					(
						:categoryid,
						:name,
						:shortdescription,
						:description,
						:languageid,
						:seo,
						:keyword_title,
						:keyword,
						:keyword_description)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('categoryid', $categoryid);
			$stmt->bindValue('name', $names['name']);
			$stmt->bindValue('shortdescription', $val['shortdescription']);
			$stmt->bindValue('description', $val['description']);
			$stmt->bindValue('languageid', $key);
			$stmt->bindValue('seo', $names['seo']);
			$stmt->bindValue('keyword_title', $val['keywordtitle']);
			$stmt->bindValue('keyword', $val['keyword']);
			$stmt->bindValue('keyword_description', $val['keyworddescription']);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_NEWS_ADD'), 4, $e->getMessage());
			}
		}
		
		foreach ($Data['view'] as $key => $val){
			$sql = 'INSERT INTO viewcategory (categoryid,viewid)
					VALUES (:categoryid, :viewid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('categoryid', $categoryid);
			$stmt->bindValue('viewid', $val);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_NEWS_ADD'), 4, $e->getMessage());
			}
		}
		
		Db::getInstance()->commit();
		$this->flushCache();
		
		$url = $this->registry->router->generate('admin', true, Array(
			'controller' => 'category',
			'action' => 'edit',
			'param' => $categoryid
		));
		
		App::getContainer()->get('session')->setVolatileMessage("Kategoria została zduplikowana.");
		
		$objResponse->script("window.location.href = '{$url}';");
		return $objResponse;
	}

	public function editCategory ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		
		$sql = 'UPDATE category SET
					categoryid=:categoryid,
					distinction = :distinction,
					enable = :enable
				WHERE idcategory = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		if (($Data['categoryid']) > 0){
			$stmt->bindValue('categoryid', $Data['categoryid']);
		}
		else{
			$stmt->bindValue('categoryid', NULL);
		}
		if (isset($Data['enable']) && $Data['enable'] == 1){
			$stmt->bindValue('enable', $Data['enable']);
		}
		else{
			$stmt->bindValue('enable', 0);
		}
		$stmt->bindValue('distinction', $Data['distinction']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CATEGORY_UPDATE'), 1, $e->getMessage());
			return false;
		}
		
		if ($Data['photo']['unmodified'] == 0){
			$sql = 'UPDATE category SET photoid = :photo
					WHERE idcategory = :id';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $id);
			if (($Data['photo'][0]) > 0){
				$stmt->bindValue('photo', $Data['photo'][0]);
			}
			else{
				$stmt->bindValue('photo', NULL);
			}
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CATEGORY_UPDATE'), 1, $e->getMessage());
				return false;
			}
		}
		
		DbTracker::deleteRows('categorytranslation', 'categoryid', $id);
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO categorytranslation (categoryid,name,shortdescription, description, languageid, seo, keyword_title, keyword, keyword_description)
					VALUES (:categoryid,:name,:shortdescription, :description, :languageid, :seo, :keyword_title, :keyword, :keyword_description)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('categoryid', $id);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('shortdescription', $Data['shortdescription'][$key]);
			$stmt->bindValue('description', $Data['description'][$key]);
			$stmt->bindValue('languageid', $key);
			$stmt->bindValue('seo', $Data['seo'][$key]);
			$stmt->bindValue('keyword_title', $Data['keywordtitle'][$key]);
			$stmt->bindValue('keyword', $Data['keyword'][$key]);
			$stmt->bindValue('keyword_description', $Data['keyworddescription'][$key]);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_NEWS_ADD'), 4, $e->getMessage());
			}
		}
		
		DbTracker::deleteRows('viewcategory', 'categoryid', $id);
		
		foreach ($Data['view'] as $key => $val){
			$sql = 'INSERT INTO viewcategory (categoryid,viewid)
					VALUES (:categoryid, :viewid)';
			$stmt = Db::getInstance()->prepare($sql);
			
			$stmt->bindValue('categoryid', $id);
			$stmt->bindValue('viewid', $val);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CATEGORY_VIEW_ADD'), 4, $e->getMessage());
			}
		}
		
		DbTracker::deleteRows('productcategory', 'categoryid', $id);
		
		if (! empty($Data['products'])){
			foreach ($Data['products'] as $key => $val){
				$sql = 'INSERT INTO productcategory (productid, categoryid)
						VALUES (:productid, :categoryid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('productid', $val);
				$stmt->bindValue('categoryid', $id);
				
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_PRODUCT_CATEGORY_ADD'), 11, $e->getMessage());
				}
			}
		}
		
		$sql = 'UPDATE category SET enable = :enable WHERE idcategory IN (SELECT categoryid FROM categorypath WHERE ancestorcategoryid = :id)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		if (isset($Data['enable']) && $Data['enable'] == 1){
			$stmt->bindValue('enable', $Data['enable']);
		}
		else{
			$stmt->bindValue('enable', 0);
		}
		$stmt->execute();
		
		Db::getInstance()->commit();
		
		Event::dispatch($this, 'admin.category.model.save', Array(
			'id' => $id,
			'data' => $Data
		));
		
		$this->getCategoriesPathById();
		$this->flushCache();
		return true;
	}

	public function addCategory ($Data)
	{
		$sql = 'INSERT INTO category (categoryid)
					VALUES (:categoryid)';
		$stmt = Db::getInstance()->prepare($sql);
		if ($Data['categoryid'] != 0){
			$stmt->bindValue('categoryid', $Data['categoryid']);
		}
		else{
			$stmt->bindValue('categoryid', NULL);
		}
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CATEGORY_ADD'), 3003, $e->getMessage());
		}
		
		$categoryid = Db::getInstance()->lastInsertId();
		
		$this->getCategoriesPathById();
		
		if ($Data['photo']['unmodified'] == 0){
			$sql = 'UPDATE category SET photoid = :photo
					WHERE idcategory = :id';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $categoryid);
			if (($Data['photo'][0]) > 0){
				$stmt->bindValue('photo', $Data['photo'][0]);
			}
			else{
				$stmt->bindValue('photo', NULL);
			}
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CATEGORY_UPDATE'), 1, $e->getMessage());
				return false;
			}
		}
		else{
			$sql = 'UPDATE category SET photoid = (SELECT photoid FROM category WHERE idcategory = :previous)
					WHERE idcategory = :id';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $categoryid);
			$stmt->bindValue('previous', $this->registry->core->getParam());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CATEGORY_UPDATE'), 1, $e->getMessage());
				return false;
			}
		}
		
		$seo = App::getModel('seo')->doAJAXCreateSeoCategory(Array(
			'name' => $Data['name'],
			'language' => Helper::getLanguageId()
		));
		
		$sql = 'INSERT INTO categorytranslation (categoryid,name,seo,shortdescription, description, languageid)
				VALUES (:categoryid,:name,:seo,:shortdescription, :description, :languageid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('categoryid', $categoryid);
		$stmt->bindValue('name', Core::clearNonAlpha($Data['name']));
		$stmt->bindValue('seo', $seo['seo']);
		$stmt->bindValue('shortdescription', $Data['shortdescription']);
		$stmt->bindValue('description', $Data['description']);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		
		$views = Helper::getViewIds();
		
		foreach ($views as $key => $val){
			if ($val > 0){
				$sql = 'INSERT INTO viewcategory (categoryid,viewid)
					VALUES (:categoryid, :viewid)';
				$stmt = Db::getInstance()->prepare($sql);
				
				$stmt->bindValue('categoryid', $categoryid);
				$stmt->bindValue('viewid', $val);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_CATEGORY_VIEW_ADD'), 4, $e->getMessage());
				}
			}
		}
		
		$this->flushCache();
		return $categoryid;
	}

	public function getPhotoCategoryById ($id)
	{
		$sql = 'SELECT photoid
					FROM category C
					LEFT JOIN file F ON F.idfile = C.photoid
					WHERE C.idcategory=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data[] = $rs['photoid'];
		}
		return $Data;
	}

	public function getPhotos (&$category)
	{
		if (! is_array($category)){
			throw new Exception('Wrong array given');
		}
		foreach ($category['photo'] as $photo){
			$category['photo']['small'][] = App::getModel('gallery')->getSmallImageById($photo['photoid']);
		}
	}

	public function getChildCategoriesTreeById ($id, &$childCategories = NULL)
	{
		$sql = 'SELECT idcategory FROM category WHERE categoryid = :categoryid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('categoryid', $id);
		$stmt->execute();
		if ($childCategories === NULL){
			$childCategories = Array();
		}
		while ($rs = $stmt->fetch()){
			$childCategories[] = $rs['idcategory'];
			$this->getChildCategoriesTreeById($rs['idcategory'], $childCategories);
		}
		return array_reverse($childCategories);
	}

	public function getParentCategoriesTreeById ($id, &$parentCategories = NULL)
	{
		foreach ($id as $key => $val){
			$id[$key] = (int) $val;
		}
		$sql = 'SELECT categoryid FROM category WHERE idcategory IN (' . implode(',', $id) . ')';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		if ($parentCategories === NULL){
			$parentCategories = Array();
		}
		while ($rs = $stmt->fetch()){
			$parentCategories[] = $rs['categoryid'];
			$this->getParentCategoriesTreeById(Array(
				$rs['categoryid']
			), $parentCategories);
		}
		return array_reverse($parentCategories);
	}

	public function getChildCategories ($parentCategory = 0, $active = Array())
	{
		$Data = Array();
		
		if ($parentCategory == 0){
			if (count($active) > 0){
				$parentTree = $this->getParentCategoriesTreeById($active);
				
				foreach ($parentTree as $key => $val){
					$parentTree[$key] = (int) $val;
				}
				$sql = 'SELECT
							C.idcategory AS id,
							C.distinction,
							C.categoryid AS parent,
							CT.name AS categoryname,
							(SELECT COUNT( idcategory )
								FROM category
								WHERE categoryid = C.idcategory
							) AS haschildren,
							(SELECT count(productid) FROM productcategory WHERE categoryid = idcategory) AS qry
						FROM category C
						LEFT JOIN categorytranslation CT ON CT.categoryid = C.idcategory AND CT.languageid = :languageid
						LEFT JOIN viewcategory VC ON VC.categoryid = C.idcategory
						WHERE (C.categoryid IN (' . implode(',', $parentTree) . ') OR C.categoryid IS NULL)
				';
				if (Helper::getViewId() > 0){
					$sql .= ' AND VC.viewid IN (' . Helper::getViewIdsAsString() . ') ';
				}
				$sql .= 'GROUP BY C.idcategory ORDER BY C.distinction ASC';
				
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('languageid', Helper::getLanguageId());
			}
			else{
				$sql = 'SELECT
							C.idcategory AS id,
							C.distinction,
							C.categoryid AS parent,
							CT.name AS categoryname,
							(SELECT COUNT( idcategory )
								FROM category
								WHERE categoryid = C.idcategory
							) AS haschildren,
							(SELECT count(productid) FROM productcategory WHERE categoryid = idcategory) AS qry
						FROM category C
						LEFT JOIN categorytranslation CT ON CT.categoryid = C.idcategory AND CT.languageid = :languageid
						LEFT JOIN viewcategory VC ON VC.categoryid = C.idcategory
						WHERE C.categoryid IS :parent
					';
				if (Helper::getViewId() > 0){
					$sql .= ' AND VC.viewid IN (' . Helper::getViewIdsAsString() . ') ';
				}
				$sql .= 'GROUP BY C.idcategory ORDER BY C.distinction ASC';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('languageid', Helper::getLanguageId());
				if ((! isset($parentTree) || empty($parentTree)) && empty($parentCategory)){
					$stmt->bindValue('parent', NULL);
				}
				else{
					$stmt->bindValue('parent', $parentCategory);
				}
			}
		}
		else{
			$sql = 'SELECT
						C.idcategory AS id,
						C.distinction,
						C.categoryid AS parent,
						CT.name AS categoryname,
						(SELECT COUNT( idcategory )
							FROM category
							WHERE categoryid = C.idcategory
						) AS haschildren,
						(SELECT count(productid) FROM productcategory WHERE categoryid = idcategory) AS qry
					FROM category C
					LEFT JOIN categorytranslation CT ON CT.categoryid = C.idcategory AND CT.languageid = :languageid
					LEFT JOIN viewcategory VC ON VC.categoryid = C.idcategory
					WHERE C.categoryid = :parent
					';
			if (Helper::getViewId() > 0){
				$sql .= ' AND VC.viewid IN (' . Helper::getViewIdsAsString() . ') ';
			}
			$sql .= 'GROUP BY C.idcategory ORDER BY C.distinction ASC';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('languageid', Helper::getLanguageId());
			if ((! isset($parentTree) || empty($parentTree)) && empty($parentCategory)){
				$stmt->bindValue('parent', NULL);
			}
			else{
				$stmt->bindValue('parent', $parentCategory);
			}
		}
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = Array(
				'id' => $rs['id'],
				'name' => $rs['categoryname'] . ' (' . $rs['qry'] . ')',
				'hasChildren' => ($rs['haschildren'] > 0) ? true : false,
				'parent' => ($rs['parent'] == 0) ? NULL : $rs['parent'],
				'weight' => $rs['distinction']
			);
		}
		return $Data;
	}

	public function getParentCategories ($parentCategory = 0)
	{
		$sql = 'SELECT
					C.idcategory AS id,
					C.distinction,
					CT.name AS categoryname
				FROM category C
				LEFT JOIN categorytranslation CT ON CT.categoryid = C.idcategory AND CT.languageid = :languageid
				LEFT JOIN viewcategory VC ON VC.categoryid = C.idcategory
				WHERE C.categoryid IS NULL
		';
		
		if (Helper::getViewId() > 0){
			$sql .= ' AND VC.viewid IN (' . implode(',', Helper::getViewIds()) . ') ';
		}
		$sql .= 'GROUP BY C.idcategory ORDER BY C.distinction ASC';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = Array(
				'id' => $rs['id'],
				'name' => $rs['categoryname'],
				'hasChildren' => false,
				'parent' => null,
				'weight' => $rs['distinction']
			);
		}
		return $Data;
	}

	public function getParentCategoriesPathById ($id, &$parentCategories = NULL)
	{
		$sql = 'SELECT categoryid FROM category WHERE idcategory = :id AND categoryid IS NOT NULL';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		if ($parentCategories === NULL){
			$parentCategories = Array();
		}
		while ($rs = $stmt->fetch()){
			$parentCategories[] = $rs['categoryid'];
			$this->getParentCategoriesPathById($rs['categoryid'], $parentCategories);
		}
		return array_reverse($parentCategories);
	}

	public function getCategoriesPathById ()
	{
		Db::getInstance()->beginTransaction();
		
		$sql = 'TRUNCATE categorypath';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		
		$sql = 'SELECT idcategory AS id, categoryid AS parent FROM category';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$Data = $stmt->fetchAll();
		$parents = Array();
		foreach ($Data as $category){
			if ($category['parent']){
				$parents[$category['id']] = $category['parent'];
			}
			else{
				$parents[$category['id']] = null;
			}
		}
		$alreadyAdded = Array();
		foreach ($parents as $category => $ancestor){
			$order = 0;
			$ancestor = $category;
			for ($i = 0; $i < 50; $i ++){
				if (! isset($alreadyAdded[$category]) || ! isset($alreadyAdded[$category][$ancestor]) || ! $alreadyAdded[$category][$ancestor]){
					$sql = '
							INSERT INTO categorypath
							SET
								categoryid = :categoryid,
								ancestorcategoryid = :ancestorcategoryid,
								`order` = :order
						';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('categoryid', $category);
					$stmt->bindValue('ancestorcategoryid', $ancestor);
					$stmt->bindValue('order', $order ++);
					$stmt->execute();
					$alreadyAdded[$category][$ancestor] = true;
				}
				if ($parents[$ancestor] == null){
					break;
				}
				$ancestor = $parents[$ancestor];
			}
		}
		
		Db::getInstance()->commit();
		$this->flushCache();
	}

	public function checkParentValue ($value, $params)
	{
		if ($value == $params['categoryid']){
			return false;
		}
		return true;
	}

	public function flushCache ()
	{
		App::getContainer()->get('cache')->delete('categories');
		App::getContainer()->get('cache')->delete('contentcategory');
		App::getContainer()->get('cache')->delete('sitemapcategories');
	}

	public function getProductsDataGrid ($id)
	{
		$sql = "SELECT
					productid
 				FROM productcategory
				WHERE categoryid =:id
				GROUP BY productid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['productid'];
		}
		return $Data;
	}

	public function getCategoryAll ()
	{
		$sql = 'SELECT
					C.idcategory AS id,
					C.categoryid AS parent,
					CT.name as categoryname,
					CT.seo as seo,
					CT.keyword_title,
					CT.keyword,
					CT.keyword_description,
					C.distinction
				FROM category C
				LEFT JOIN categorytranslation CT ON CT.categoryid = C.idcategory AND CT.languageid = :languageid
		';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', App::getContainer()->get('session')->getActiveLanguageId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'categoryname' => $rs['categoryname'],
				'seo' => $rs['seo'],
				'keywordtitle' => $rs['keyword_title'],
				'keyword' => $rs['keyword'],
				'keyworddescription' => $rs['keyword_description'],
				'distinction' => $rs['distinction'],
				'parent' => $rs['parent']
			);
		}
		return $Data;
	}
}