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
 * $Id: producer.php 552 2011-10-08 15:56:59Z gekosale $ 
 */

namespace Gekosale\Plugin;

class ProducerModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('producer', Array(
			'idproducer' => Array(
				'source' => 'P.idproducer'
			),
			'name' => Array(
				'source' => 'PT.name',
				'prepareForAutosuggest' => true
			),
			'adddate' => Array(
				'source' => 'P.adddate'
			)
		));
		
		$datagrid->setFrom('
			producer P
			LEFT JOIN producertranslation PT ON PT.producerid = P.idproducer AND PT.languageid = :languageid
			LEFT JOIN producerview PV ON PV.producerid = P.idproducer
		');
		
		if (Helper::getViewId() > 0){
			$datagrid->setAdditionalWhere('
				PV.viewid IN (' . Helper::getViewIdsAsString() . ')
			');
		}
		
		$datagrid->setGroupBy('
			P.idproducer
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

	public function doAJAXDeleteProducer ($datagrid, $id)
	{
		return $this->getDatagrid()->deleteRow($datagrid, $id, Array(
			$this,
			'deleteProducer'
		), $this->getName());
	}

	public function deleteProducer ($id)
	{
		DbTracker::deleteRows('producer', 'idproducer', $id);
	}

	public function getDataById ($id)
	{
		$Data = $this->getProducerView($id);
		
		if (isset($Data['id'])){
			return Array(
				'required_data' => Array(
					'language_data' => $Data['language'],
					'deliverer' => $Data['deliverers']
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

	public function getProducerView ($id)
	{
		$sql = "SELECT 
					idproducer AS id,
					photoid
				FROM producer 
				WHERE idproducer = :id
		";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data = Array(
				'language' => $this->getProducerTranslationById($id),
				'id' => $rs['id'],
				'photo' => $rs['photoid'],
				'deliverers' => $this->ProducerDelivererIds($id),
				'view' => $this->getProducerViews($id)
			);
		}
		return $Data;
	}

	public function getProducerTranslationById ($id)
	{
		$sql = "SELECT 
					name, 
					seo,
					description, 
					languageid,
					keyword_title,
					keyword,
					keyword_description
				FROM producertranslation
				WHERE producerid = :id ";
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

	public function getProducerViews ($id)
	{
		$sql = "SELECT viewid FROM producerview WHERE producerid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['viewid'];
		}
		return $Data;
	}

	public function getProducerDelivererView ($id)
	{
		$sql = "SELECT DT.name AS deliverername
					FROM producerdeliverer PD
					LEFT JOIN deliverer D ON D.iddeliverer = PD.delivererid
					LEFT JOIN deliverertranslation DT ON D.iddeliverer = DT.delivererid AND DT.languageid = :language
					WHERE PD.producerid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('language', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data['deliverername'][] = $rs['deliverername'];
		}
		return $Data;
	}

	public function getProducerAll ()
	{
		$sql = 'SELECT P.idproducer AS id,PT.name
				FROM producer P
				LEFT JOIN producertranslation PT ON PT.producerid = P.idproducer AND PT.languageid = :language
				LEFT JOIN producerview PV ON PV.producerid = P.idproducer
				WHERE PV.viewid IN (' . Helper::getViewIdsAsString() . ')';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('language', Helper::getLanguageId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $rs['name']
			);
		}
		return $Data;
	}

	public function getProducerToSelect ()
	{
		$Data = $this->getProducerAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return $tmp;
	}

	public function getProducerAsExchangeOptions ()
	{
		$Data = $this->getProducerAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[] = Array(
				'sValue' => $key['id'],
				'sLabel' => $key['name']
			);
		}
		return $tmp;
	}

	public function ProducerDeliverer ($id)
	{
		$sql = 'SELECT D.iddeliverer AS id, DT.name
					FROM producerdeliverer PD
					LEFT JOIN deliverer D ON PD.delivererid = D.iddeliverer
					LEFT JOIN deliverertranslation DT ON D.iddeliverer = DT.delivererid AND DT.languageid = :language
					WHERE PD.producerid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('language', Helper::getLanguageId());
		$stmt->bindValue('id', $id);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function ProducerDelivererIds ($id)
	{
		$Data = $this->ProducerDeliverer($id);
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[] = $key['id'];
		}
		return $tmp;
	}

	public function addEmptyProducer ($request)
	{
		$sql = 'SELECT producerid FROM producertranslation WHERE name = :name AND languageid = :languageid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $request['name']);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$rs = $stmt->fetch();
		
		if ($rs){
			$id = $rs['producerid'];
		}
		else{
			
			$id = $this->addProducer(Array());
			$seo = App::getModel('seo')->doAJAXCreateSeo($request);
			$sql = 'INSERT INTO producertranslation SET
						producerid = :producerid,
						name = :name,
						seo = :seo,
						languageid = :languageid
			';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('producerid', $id);
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
			'options' => $this->getProducerAsExchangeOptions()
		);
	}

	public function save ($Data, $id = 0)
	{
		Db::getInstance()->beginTransaction();
		try{
			if ($id > 0){
				$this->deleteView($id);
				$this->deleteTranslation($id);
				$this->deleteDeliverer($id);
				$this->updateProducer($Data, $id);
				$this->updateProducerPhoto($Data, $id);
			}
			else{
				$id = $this->addProducer($Data);
			}
			
			$this->addDeliverer($Data['deliverer'], $id);
			$this->addView($Data['view'], $id);
			$this->addTranslation($Data, $id);
			
			Event::dispatch($this, 'admin.producer.model.save', Array(
				'id' => $id,
				'data' => $Data
			));
		
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCER_SAVE'), 10, $e->getMessage());
		}
		
		Db::getInstance()->commit();
	}

	public function addProducer ($Data)
	{
		$sql = 'INSERT INTO producer (photoid) VALUES (:photoid)';
		$stmt = Db::getInstance()->prepare($sql);
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

	public function updateProducer ($Data, $id)
	{
	
	}

	public function updateProducerPhoto ($Data, $id)
	{
		if (isset($Data['photo']['unmodified']) && $Data['photo']['unmodified']){
			return;
		}
		
		$sql = 'UPDATE producer SET photoid = :photo WHERE idproducer = :id';
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
			$sql = 'INSERT INTO producerview (producerid, viewid)
			VALUES (:producerid, :viewid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('producerid', $id);
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
		$sql = 'DELETE FROM producerview WHERE producerid =:id';
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
			$sql = 'INSERT INTO producertranslation (
						producerid,
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
						:producerid,
						:name, 
						:seo,
						:description, 
						:keyword_title,
						:keyword,
						:keyword_description,
						:languageid
					)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('producerid', $id);
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
		$sql = 'DELETE FROM producertranslation WHERE producerid =:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	protected function addDeliverer ($Data, $id)
	{
		foreach ($Data as $value){
			$sql = 'INSERT INTO producerdeliverer (delivererid, producerid) VALUES (:delivererid, :producerid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('delivererid', $value);
			$stmt->bindValue('producerid', $id);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCER_DELIVERER_ADD'), 15, $e->getMessage());
			}
		}
	}

	protected function deleteDeliverer ($id)
	{
		$sql = 'DELETE FROM producerdeliverer WHERE producerid = :id';
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