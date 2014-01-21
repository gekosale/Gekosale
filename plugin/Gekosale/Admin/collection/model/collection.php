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
 * $Revision: 552 $
 * $Author: gekosale $
 * $Date: 2011-10-08 17:56:59 +0200 (So, 08 paź 2011) $
 * $Id: collection.php 552 2011-10-08 15:56:59Z gekosale $ 
 */
namespace Gekosale;

class CollectionModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('collection', Array(
			'idcollection' => Array(
				'source' => 'P.idcollection'
			),
			'name' => Array(
				'source' => 'PT.name',
				'prepareForAutosuggest' => true
			),
			'producer' => Array(
				'source' => 'PRT.name',
				'prepareForSelect' => true
			),
			'adddate' => Array(
				'source' => 'P.adddate'
			)
		));
		
		$datagrid->setFrom('
			collection P
			LEFT JOIN collectiontranslation PT ON PT.collectionid = P.idcollection AND PT.languageid = :languageid
			LEFT JOIN collectionview PV ON PV.collectionid = P.idcollection
			LEFT JOIN producertranslation PRT ON P.producerid = PRT.producerid AND PRT.languageid = :languageid
		');
		
		if (Helper::getViewId() > 0){
			$datagrid->setAdditionalWhere('
				PV.viewid IN (' . Helper::getViewIdsAsString() . ')
			');
		}
		
		$datagrid->setGroupBy('
			P.idcollection
		');
	}

	public function getNameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getDataForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteCollection ($datagrid, $id)
	{
		return $this->getDatagrid()->deleteRow($datagrid, $id, Array(
			$this,
			'deleteCollection'
		), $this->getName());
	}

	public function deleteCollection ($id)
	{
		DbTracker::deleteRows('collection', 'idcollection', $id);
	}

	public function getDataById ($id)
	{
		$Data = $this->getCollectionView($id);
		
		if (isset($Data['id'])){
			return Array(
				'required_data' => Array(
					'language_data' => $Data['language'],
					'producerid' => $Data['producerid']
				),
				'meta_data' => Array(
					'language_data' => $Data['language']
				),
				'photos_pane' => Array(
					'photo' => $Data['photo']
				),
				'view_data' => Array(
					'view' => $Data['view']
				)
			);
		}
		else{
			return Array();
		}
	}

	public function getCollectionView ($id)
	{
		$sql = "SELECT 
					idcollection AS id,
					photoid,
					producerid
				FROM collection 
				WHERE idcollection = :id
		";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data = Array(
				'language' => $this->getCollectionTranslationById($id),
				'id' => $rs['id'],
				'photo' => $rs['photoid'],
				'producerid' => $rs['producerid'],
				'view' => $this->getCollectionViews($id)
			);
		}
		return $Data;
	}

	public function getCollectionTranslationById ($id)
	{
		$sql = "SELECT 
					name, 
					seo,
					description, 
					languageid,
					keyword_title,
					keyword,
					keyword_description
				FROM collectiontranslation
				WHERE collectionid = :id ";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$langid = $rs['languageid'];
			$Data[$langid] = Array(
				'name' => $rs['name'],
				'seo' => $rs['seo'],
				'description' => $rs['description'],
				'keyword_title' => $rs['keyword_title'],
				'keyword' => $rs['keyword'],
				'keyword_description' => $rs['keyword_description']
			);
		}
		return $Data;
	}

	public function getCollectionViews ($id)
	{
		$sql = "SELECT viewid FROM collectionview WHERE collectionid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['viewid'];
		}
		return $Data;
	}

	public function getCollectionAll ($producerid = 0)
	{
		$sql = 'SELECT P.idcollection AS id,PT.name
				FROM collection P
				LEFT JOIN collectiontranslation PT ON PT.collectionid = P.idcollection AND PT.languageid = :language
				LEFT JOIN collectionview PV ON PV.collectionid = P.idcollection
				WHERE PV.viewid IN (' . Helper::getViewIdsAsString() . ') AND IF(:producerid > 0, P.producerid = :producerid, 1)';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('language', Helper::getLanguageId());
		$stmt->bindValue('producerid', $producerid);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $rs['name']
			);
		}
		return $Data;
	}

	public function getCollectionToSelect ($producerid)
	{
		$Data = $this->getCollectionAll($producerid);
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return $tmp;
	}

	public function getCollectionAsExchangeOptions ($producerid)
	{
		$Data = $this->getCollectionAll($producerid);
		$tmp = Array();
		$tmp[0] = $this->trans('TXT_CHOOSE_SELECT');
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return \FormEngine\Option::Make($tmp);
	}

	public function addEmptyCollection ($request)
	{
		$sql = 'SELECT collectionid FROM collectiontranslation WHERE name = :name AND languageid = :languageid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $request['name']);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$rs = $stmt->fetch();
		
		if ($rs){
			$id = $rs['collectionid'];
		}
		else{
			
			$id = $this->addCollection(Array());
			$seo = App::getModel('seo')->doAJAXCreateSeo($request);
			$sql = 'INSERT INTO collectiontranslation SET
						collectionid = :collectionid,
						name = :name,
						seo = :seo,
						languageid = :languageid
			';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('collectionid', $id);
			$stmt->bindValue('name', $request['name']);
			$stmt->bindValue('seo', $seo['seo']);
			$stmt->bindValue('languageid', Helper::getLanguageId());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCER_TRANSLATION_EDIT'), 15, $e->getMessage());
			}
			
			$this->addView(Helper::getViewIdsDefault(), $id);
		}
		
		return Array(
			'id' => $id,
			'options' => $this->getCollectionAsExchangeOptions()
		);
	}

	public function save ($Data, $id = 0)
	{
		Db::getInstance()->beginTransaction();
		try{
			if ($id > 0){
				$this->deleteView($id);
				$this->deleteTranslation($id);
				$this->updateCollection($Data, $id);
				$this->updateCollectionPhoto($Data, $id);
			}
			else{
				$id = $this->addCollection($Data);
			}
			
			$this->addView($Data['view'], $id);
			$this->addTranslation($Data, $id);
			
			Event::dispatch($this, 'admin.collection.model.save', Array(
				'id' => $id,
				'data' => $Data
			));
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCER_SAVE'), 10, $e->getMessage());
		}
		
		Db::getInstance()->commit();
	}

	public function addCollection ($Data)
	{
		$sql = 'INSERT INTO collection (photoid, producerid) VALUES (:photoid, :producerid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('producerid', $Data['producerid']);
		if (isset($Data['photo'][0]) && ($Data['photo'][0]) > 0){
			$stmt->bindValue('photoid', $Data['photo'][0]);
		}
		else{
			$stmt->bindValue('photoid', NULL);
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCER_ADD'), 15, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function updateCollection ($Data, $id)
	{
		$sql = 'UPDATE collection SET producerid = :producerid WHERE idcollection = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('producerid', $Data['producerid']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCER_EDIT'), 15, $e->getMessage());
		}
	}

	public function updateCollectionPhoto ($Data, $id)
	{
		if (isset($Data['photo']['unmodified']) && $Data['photo']['unmodified']){
			return;
		}
		
		$sql = 'UPDATE collection SET photoid = :photo WHERE idcollection = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		if (isset($Data['photo'][0]) && ($Data['photo'][0]) > 0){
			$stmt->bindValue('photo', $Data['photo'][0]);
		}
		else{
			$stmt->bindValue('photo', NULL);
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCER_EDIT'), 15, $e->getMessage());
		}
	}

	public function addView ($Data, $id)
	{
		foreach ($Data as $value){
			$sql = 'INSERT INTO collectionview (collectionid, viewid)
			VALUES (:collectionid, :viewid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('collectionid', $id);
			$stmt->bindValue('viewid', $value);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCER_VIEW_EDIT'), 4, $e->getMessage());
			}
		}
	}

	public function deleteView ($id)
	{
		$sql = 'DELETE FROM collectionview WHERE collectionid =:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function addTranslation ($Data, $id)
	{
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO collectiontranslation (
						collectionid,
						name, 
						seo, 
						description,
						keyword_title,
						keyword,
						keyword_description,
						languageid
					)
					VALUES 
					(
						:collectionid,
						:name, 
						:seo,
						:description, 
						:keyword_title,
						:keyword,
						:keyword_description,
						:languageid
					)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('collectionid', $id);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('seo', $Data['seo'][$key]);
			$stmt->bindValue('description', $Data['description'][$key]);
			$stmt->bindValue('keyword_title', $Data['keyword_title'][$key]);
			$stmt->bindValue('keyword', $Data['keyword'][$key]);
			$stmt->bindValue('keyword_description', $Data['keyword_description'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCER_TRANSLATION_EDIT'), 15, $e->getMessage());
			}
		}
	}

	public function deleteTranslation ($id)
	{
		$sql = 'DELETE FROM collectiontranslation WHERE collectionid =:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}
}