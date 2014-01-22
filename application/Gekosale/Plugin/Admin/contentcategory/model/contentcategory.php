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
 * $Revision: 513 $
 * $Author: gekosale $
 * $Date: 2011-09-08 09:06:11 +0200 (Cz, 08 wrz 2011) $
 * $Id: contentcategory.php 513 2011-09-08 07:06:11Z gekosale $ 
 */
namespace Gekosale\Plugin;

class ContentCategoryModel extends Component\Model
{

	public function doAJAXDeleteContentCategory ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteContentCategory'
		), $this->getName());
	}

	public function deleteContentCategory ($id)
	{
		DbTracker::deleteRows('contentcategory', 'idcontentcategory', $id);
		$this->flushCache();
	}

	public function getContentCategoryALL ()
	{
		$sql = 'SELECT 
					C.idcontentcategory AS id, 
					CT.name,
					C.hierarchy,
					C.contentcategoryid AS parent
				FROM contentcategory C
				LEFT JOIN contentcategorytranslation CT ON CT.contentcategoryid = C.idcontentcategory AND CT.languageid = :languageid
				LEFT JOIN contentcategoryview CCV ON CCV.contentcategoryid = C.idcontentcategory AND CCV.viewid = :viewid
				WHERE IF(:viewid > 0, CCV.viewid = :viewid, 1)
				ORDER BY C.hierarchy ASC';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = Array(
				'id' => $rs['id'],
				'name' => $rs['name'],
				'weight' => $rs['hierarchy'],
				'parent' => $rs['parent']
			);
		}
		return $Data;
	}

	public function addEmptyCategory ($request)
	{
		$data = Array(
			'contentcategoryid' => isset($request['parent']) ? (int) $request['parent'] : 0,
			'header' => 1,
			'footer' => 1,
			'name' => (isset($request['name']) && strlen($request['name'])) ? $request['name'] : $this->trans('TXT_NEW_CATEGORY'),
			'view' => Helper::getViewIdsDefault()
		);
		return Array(
			'id' => $this->addNewContentCategory($data)
		);
	}

	public function addNewContentCategory ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newContentCategoryId = $this->addContentCategory($Data);
			if (is_array($Data['view']) && ! empty($Data['view'])){
				$this->addContentCategoryView($Data['view'], $newContentCategoryId);
			}
			$this->addContentCategoryTranslation($Data, $newContentCategoryId);
			$this->flushCache();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CONTENT_CATEGORY_ADD'), 11, $e->getMessage());
		}
		Db::getInstance()->commit();
		return $newContentCategoryId;
	}

	public function addContentCategory ($Data)
	{
		$sql = 'INSERT INTO contentcategory(contentcategoryid, header, footer)
				VALUES (:contentcategoryid, :header, :footer)';
		$stmt = Db::getInstance()->prepare($sql);
		if ($Data['contentcategoryid'] == 0){
			$stmt->bindValue('contentcategoryid', NULL);
		}
		else{
			$stmt->bindValue('contentcategoryid', $Data['contentcategoryid']);
		}
		if (isset($Data['header']) && $Data['header'] == 1){
			$stmt->bindValue('header', 1);
		}
		else{
			$stmt->bindValue('header', 0);
		}
		if (isset($Data['footer']) && $Data['footer'] == 1){
			$stmt->bindValue('footer', 1);
		}
		else{
			$stmt->bindValue('footer', 0);
		}
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CONTENT_CATGEORY_ADD'), 4, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addContentCategoryTranslation ($Data, $id)
	{
		$sql = 'INSERT INTO contentcategorytranslation (contentcategoryid, name, languageid)
				VALUES (:contentcategoryid,:name,:languageid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('contentcategoryid', $id);
		$stmt->bindValue('name', $Data['name']);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CONTENT_CATEGORY_TRANSLATION_ADD'), 13, $e->getMessage());
		}
	}

	public function addContentCategoryView ($Data, $contentcategoryid)
	{
		foreach ($Data as $value){
			$sql = 'INSERT INTO contentcategoryview (contentcategoryid, viewid)
						VALUES (:contentcategoryid, :viewid)';
			$stmt = Db::getInstance()->prepare($sql);
			
			$stmt->bindValue('contentcategoryid', $contentcategoryid);
			$stmt->bindValue('viewid', $value);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CONTENT_CATGEORY_VIEW_ADD'), 4, $e->getMessage());
			}
		}
	}

	public function getContentCategoryView ($id)
	{
		$sql = "SELECT 
					C.idcontentcategory AS id,
					C.header,
					C.footer,
					CT.name, 
					C.contentcategoryid as catid,
					C.hierarchy,
					C.redirect,
					C.redirect_route,
					C.redirect_url
				FROM contentcategory C 
				LEFT JOIN contentcategorytranslation CT ON CT.contentcategoryid = C.idcontentcategory AND CT.languageid = :languageid
				WHERE C.idcontentcategory = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'id' => $rs['id'],
				'language' => $this->getContentCategoryTranslation($id),
				'contentcategory' => $rs['catid'],
				'header' => $rs['header'],
				'footer' => $rs['footer'],
				'seo' => strtolower(Core::clearSeoUTF($rs['name'])),
				'redirect' => $rs['redirect'],
				'redirect_route' => $rs['redirect_route'],
				'redirect_url' => $rs['redirect_url'],
				'view' => $this->getContentCategoryViews($id),
				'next' => $this->getNextCategoryId($rs['id'], $rs['catid'], $rs['hierarchy'])
			);
		}
		else{
			throw new CoreException($this->trans('ERR_CONTENT_CATEGORY_NO_EXIST'));
		}
		return $Data;
	}

	public function getNextCategoryId ($id, $parent, $distinction)
	{
		if ($parent == NULL){
			$sql = 'SELECT
						C.idcontentcategory AS id
					FROM contentcategory C
					WHERE C.idcontentcategory !=:id AND C.contentcategoryid IS NULL AND C.hierarchy > :hierarchy
					ORDER BY C.hierarchy ASC
					LIMIT 1';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $id);
			$stmt->bindValue('hierarchy', $distinction);
			$stmt->execute();
			$rs = $stmt->fetch();
		}
		else{
			$sql = 'SELECT
						C.idcontentcategory AS id
					FROM contentcategory C
					WHERE C.idcontentcategory !=:id AND C.contentcategoryid = :parent AND C.hierarchy > :hierarchy
					ORDER BY C.hierarchy ASC
					LIMIT 1';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $id);
			$stmt->bindValue('parent', $parent);
			$stmt->bindValue('hierarchy', $distinction);
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

	public function changeCategoryOrder ($request)
	{
		if (! isset($request['items']) || ! is_array($request['items'])){
			throw new Exception('No data received.');
		}
		$sql = 'UPDATE
					contentcategory
				SET
					contentcategoryid = :contentcategoryid,
					hierarchy = :hierarchy
				WHERE
					idcontentcategory = :id';
		foreach ($request['items'] as $item){
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $item['id']);
			$stmt->bindValue('hierarchy', $item['weight']);
			if (! isset($item['parent']) || empty($item['parent'])){
				$stmt->bindValue('contentcategoryid', NULL);
			}
			else{
				$stmt->bindValue('contentcategoryid', $item['parent']);
			}
			$stmt->execute();
		}
		$this->flushCache();
		return Array(
			'status' => $this->trans('TXT_ORDER_SAVED')
		);
	}

	public function getContentCategoryViews ($id)
	{
		$sql = "SELECT viewid
					FROM contentcategoryview
					WHERE contentcategoryid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['viewid'];
		}
		return $Data;
	}

	public function getContentCategoryTranslation ($id)
	{
		$sql = "SELECT 
					name,
					description,
					keyword_title, 
					keyword,
					keyword_description, 
					languageid
				FROM contentcategorytranslation
				WHERE contentcategoryid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['languageid']] = Array(
				'name' => $rs['name'],
				'description' => $rs['description'],
				'keyword_title' => $rs['keyword_title'],
				'keyword' => $rs['keyword'],
				'keyword_description' => $rs['keyword_description']
			);
		}
		return $Data;
	}

	public function editContentCategory ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->updateContentCategory($Data, $id);
			$this->updateContentCategoryTranslation($Data, $id);
			$this->updateContentCategoryView($Data['view'], $id);
			$this->flushCache();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CONTENT_CATEGORY_EDIT'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function updateContentCategory ($Data, $id)
	{
		$sql = 'UPDATE contentcategory SET 
					header = :header,
					footer = :footer,
					contentcategoryid = :contentcategoryid,
					redirect = :redirect,
					redirect_route = :redirect_route,
					redirect_url = :redirect_url
				WHERE idcontentcategory =:id';
		$stmt = Db::getInstance()->prepare($sql);
		if ($Data['contentcategoryid'] == 0){
			$stmt->bindValue('contentcategoryid', NULL);
		}
		else{
			$stmt->bindValue('contentcategoryid', $Data['contentcategoryid']);
		}
		
		if ($Data['redirect'] == 0){
			$stmt->bindValue('redirect', 0);
			$stmt->bindValue('redirect_route', '');
			$stmt->bindValue('redirect_url', '');
		}
		
		if ($Data['redirect'] == 1){
			$stmt->bindValue('redirect', 1);
			$stmt->bindValue('redirect_route', $Data['redirect_route']);
			$stmt->bindValue('redirect_url', '');
		}
		
		if ($Data['redirect'] == 2){
			$stmt->bindValue('redirect', 2);
			$stmt->bindValue('redirect_route', '');
			$stmt->bindValue('redirect_url', $Data['redirect_url']);
		}
		
		if (isset($Data['header']) && $Data['header'] == 1){
			$stmt->bindValue('header', 1);
		}
		else{
			$stmt->bindValue('header', 0);
		}
		if (isset($Data['footer']) && $Data['footer'] == 1){
			$stmt->bindValue('footer', 1);
		}
		else{
			$stmt->bindValue('footer', 0);
		}
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CONTENT_CATEGORY_EDIT'), 13, $e->getMessage());
		}
	}

	public function updateContentCategoryTranslation ($Data, $id)
	{
		DbTracker::deleteRows('contentcategorytranslation', 'contentcategoryid', $id);
		
		foreach ($Data['name'] as $key => $value){
			$sql = 'INSERT INTO contentcategorytranslation (contentcategoryid,name, description, keyword_title, keyword,keyword_description,languageid)
					VALUES (:contentcategoryid,:name,:description,:keyword_title, :keyword,:keyword_description,:languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('contentcategoryid', $id);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('description', $Data['description'][$key]);
			$stmt->bindValue('keyword_title', $Data['keyword_title'][$key]);
			$stmt->bindValue('keyword', $Data['keyword'][$key]);
			$stmt->bindValue('keyword_description', $Data['keyword_description'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CONTENT_CATEGORY_TRANSLATION_ADD'), 13, $e->getMessage());
			}
		}
	}

	public function updateContentCategoryView ($Data, $id)
	{
		DbTracker::deleteRows('contentcategoryview', 'contentcategoryid', $id);
		
		if (! empty($Data)){
			foreach ($Data as $value){
				$sql = 'INSERT INTO contentcategoryview (contentcategoryid, viewid)
							VALUES (:contentcategoryid, :viewid)';
				$stmt = Db::getInstance()->prepare($sql);
				
				$stmt->bindValue('contentcategoryid', $id);
				$stmt->bindValue('viewid', $value);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_CONTENT_CATEGORY_VIEW_EDIT'), 4, $e->getMessage());
				}
			}
		}
	}

	public function doAJAXUpdateContentCategory ($id, $hierarchy)
	{
		$sql = 'UPDATE contentcategory SET 
					hierarchy = :hierarchy
				WHERE idcontentcategory = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('hierarchy', $hierarchy);
		$stmt->execute();
		$this->flushCache();
	}

	public function flushCache ()
	{
		App::getContainer()->get('cache')->delete('contentcategory');
		App::getContainer()->get('cache')->delete('sitemapcategories');
	}
}