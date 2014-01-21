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
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: deliverer.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;

class DelivererModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('deliverer', Array(
			'iddeliverer' => Array(
				'source' => 'D.iddeliverer'
			),
			'name' => Array(
				'source' => 'DT.name',
				'prepareForAutosuggest' => true
			),
			'www' => Array(
				'source' => 'DT.www'
			),
			'adddate' => Array(
				'source' => 'D.adddate'
			)
		));
		
		$datagrid->setFrom('
			`deliverer` D
			LEFT JOIN deliverertranslation DT ON DT.delivererid = D.iddeliverer AND DT.languageid = :languageid
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

	public function getDelivererForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteDeliverer ($datagrid, $id)
	{
		return $this->getDatagrid()->deleteRow($datagrid, $id, Array(
			$this,
			'deleteDeliverer'
		), $this->getName());
	}

	public function deleteDeliverer ($id)
	{
		DbTracker::deleteRows('deliverer', 'iddeliverer', $id);
	}

	public function getDelivererView ($id)
	{
		$sql = "SELECT iddeliverer AS id, photoid as photo FROM deliverer WHERE iddeliverer=:id";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data = Array(
				'language' => $this->getDelivererTranslation($id),
				'photo' => $rs['photo']
			);
		}
		return $Data;
	}

	public function getDelivererTranslation ($id)
	{
		$sql = "SELECT name,www,email,languageid
					FROM deliverertranslation
					WHERE delivererid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['languageid']] = Array(
				'name' => $rs['name'],
				'www' => $rs['www'],
				'email' => $rs['email']
			);
		}
		return $Data;
	}

	public function getProductsForDelilverer ($id)
	{
		$sql = "SELECT PT.name, PD.delivererid as id, P.idproduct
					FROM productdeliverer PD
					LEFT JOIN producttranslation PT ON PT.productid = PD.productid AND PT.languageid=:languageid
					LEFT JOIN product P ON P.idproduct = PD.productid
					WHERE delivererid=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['idproduct'];
		}
		return $Data;
	}

	public function getDelivererAll ()
	{
		$sql = 'SELECT D.iddeliverer as id, DT.name 
					FROM deliverer D
					LEFT JOIN deliverertranslation DT ON DT.delivererid = D.iddeliverer AND DT.languageid = :language';
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

	public function getDelivererToSelect ()
	{
		$Data = $this->getDelivererAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return $tmp;
	}

	public function getDelivererAsExchangeOptions ()
	{
		$Data = $this->getDelivererAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[] = Array(
				'sValue' => $key['id'],
				'sLabel' => $key['name']
			);
		}
		return $tmp;
	}

	public function addEmptyDeliverer ($request)
	{
		$sql = 'SELECT delivererid FROM deliverertranslation WHERE name = :name AND languageid = :languageid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $request['name']);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$rs = $stmt->fetch();
		
		if ($rs){
			$id = $rs['delivererid'];
		}
		else{
			
			$id = $this->addDeliverer(Array());
			$sql = 'INSERT INTO deliverertranslation (delivererid, name, languageid)
					VALUES (:delivererid, :name, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('delivererid', $id);
			$stmt->bindValue('name', $request['name']);
			$stmt->bindValue('languageid', Helper::getLanguageId());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_DELIVERER_TRANSLATION_EDIT'), 10, $e->getMessage());
			}
		}
		
		return Array(
			'id' => $id,
			'options' => $this->getDelivererAsExchangeOptions()
		);
	}

	public function editDeliverer ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->updateDeliverer($Data, $id);
			$this->updateDelivererPhoto($Data, $id);
			$this->updateDelivererTranslation($Data, $id);
			$this->updateProductForDeliverer($Data['products'], $id);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_DELIVERER_EDIT'), 10, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function updateDeliverer ($Data, $id)
	{
	
	}

	public function updateDelivererPhoto ($Data, $id)
	{
		if (isset($Data['photo']['unmodified']) && $Data['photo']['unmodified']){
			return;
		}
		
		$sql = 'UPDATE deliverer SET photoid = :photo WHERE iddeliverer = :id';
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
			throw new CoreException($this->trans('ERR_DELIVERER_EDIT'), 10, $e->getMessage());
		}
	}

	public function updateDelivererTranslation ($Data, $id)
	{
		DbTracker::deleteRows('deliverertranslation', 'delivererid', $id);
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO deliverertranslation (delivererid, name, www, email, languageid)
						VALUES (:delivererid, :name, :www, :email, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('delivererid', $id);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('www', $Data['www'][$key]);
			$stmt->bindValue('email', $Data['email'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_DELIVERER_TRANSLATION_EDIT'), 10, $e->getMessage());
			}
		}
		return true;
	}

	public function updateProductForDeliverer ($Data, $id)
	{
		DbTracker::deleteRows('productdeliverer', 'delivererid', $id);
		
		if (! empty($Data)){
			foreach ($Data as $value){
				$sql = 'INSERT INTO productdeliverer (productid, delivererid)
							VALUES (:productid, :delivererid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('productid', $value);
				$stmt->bindValue('delivererid', $id);
				
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}
	}

	public function addNewDeliverer ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newDelivererId = $this->addDeliverer($Data);
			$this->addDelivererTranslation($Data, $newDelivererId);
			if (! empty($Data['products'])){
				$this->addProductForDeliverer($Data['products'], $newDelivererId);
			}
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_DELIVERER_ADD'), 11, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function addProductForDeliverer ($Data, $delivererId)
	{
		foreach ($Data as $value){
			$sql = 'INSERT INTO productdeliverer (productid, delivererid)
						VALUES (:productid, :delivererid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $value);
			$stmt->bindValue('delivererid', $delivererId);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_DELIVERER_PRODUCT_ADD'), 11, $e->getMessage());
			}
		}
	}

	public function addDeliverer ($Data)
	{
		$sql = 'INSERT INTO deliverer (photoid) VALUES (:photoid)';
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
			throw new CoreException($this->trans('ERR_DELIVERER_ADD'), 15, $e->getMessage());
		}
		
		return Db::getInstance()->lastInsertId();
	}

	public function addDelivererTranslation ($Data, $id)
	{
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO deliverertranslation (delivererid, name, www, email, languageid)
					VALUES (:delivererid, :name, :www, :email, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('delivererid', $id);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('www', $Data['www'][$key]);
			$stmt->bindValue('email', $Data['email'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_DELIVERER_TRANSLATION_EDIT'), 10, $e->getMessage());
			}
		}
	}

	public function getPhotoDelivererById ($id)
	{
		$sql = 'SELECT photoid FROM deliverer WHERE iddeliverer=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data[] = $rs['photoid'];
		}
		return $Data;
	}
}