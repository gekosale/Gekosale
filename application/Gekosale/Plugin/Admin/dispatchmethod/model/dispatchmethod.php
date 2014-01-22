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
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: dispatchmethod.php 619 2011-12-19 21:09:00Z gekosale $ 
 */
namespace Gekosale\Plugin;

class DispatchmethodModel extends Component\Model\Datagrid
{

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
		$this->countries = App::getModel('countrieslist')->getCountryForSelect();
	}

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('dispatchmethod', Array(
			'iddispatchmethod' => Array(
				'source' => 'D.iddispatchmethod'
			),
			'name' => Array(
				'source' => 'D.name',
				'prepareForAutosuggest' => true,
				'processLanguage' => true
			),
			'countries' => Array(
				'source' => 'D.countryids',
				'processFunction' => Array(
					$this,
					'getCountriesForDispatchmethod'
				)
			),
			'hierarchy' => Array(
				'source' => 'D.hierarchy'
			)
		));
		$datagrid->setFrom('
			dispatchmethod D
			LEFT JOIN dispatchmethodview DV ON DV.dispatchmethodid = D.iddispatchmethod
		');
		
		$datagrid->setGroupBy('
			D.iddispatchmethod
		');
		
		if (Helper::getViewId() > 0){
			$datagrid->setAdditionalWhere('
				DV.viewid IN (' . Helper::getViewIdsAsString() . ') 
			');
		}
	}

	public function getCountriesForDispatchmethod ($ids)
	{
		$countryList = Array();
		if ($ids != ''){
			$countries = explode(',', $ids);
			$countryString = '';
			foreach ($countries as $key => $country){
				if (isset($this->countries[$country])){
					$countryList[] = $this->countries[$country];
				}
			}
		}
		return (count($countryList) > 0) ? implode('<br />', $countryList) : '';
	}

	public function getNameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getDispatchmethodForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteDispatchmethod ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteDispatchmethod'
		), $this->getName());
	}

	public function deleteDispatchmethod ($id)
	{
		$sql = "SELECT COUNT(idorder) as total FROM `order` WHERE dispatchmethodid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs['total'] == 0){
			return DbTracker::deleteRows('dispatchmethod', 'iddispatchmethod', $id);
		}
		else{
			return Array(
				'error' => $this->trans('ERR_DISPATCHMETHOD_USED_IN_ORDERS')
			);
		}
	}

	/**
	 * Return specified dispatch method
	 * 
	 * @param int $id
	 *        	dispatch method id
	 * @return array
	 */
	public function getDispatchmethod ($id)
	{
		$sql = 'SELECT iddispatchmethod AS id, name, maximumweight, freedelivery, type FROM dispatchmethod where iddispatchmethod = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		
		$data = null;
		$stmt->execute();
		if ($rs = $stmt->fetch()){
			$data = Array(
				'id' => $rs['id'],
				'name' => $this->trans($rs['name']),
				'maximumweight' => $rs['maximumweight'],
				'freedelivery' => $rs['freedelivery'],
				'type' => $rs['type']
			)
			;
		}
		return $data;
	}

	public function getDispatchmethodAll ()
	{
		$sql = 'SELECT iddispatchmethod AS id, name FROM dispatchmethod';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $this->trans($rs['name'])
			);
		}
		return $Data;
	}

	public function getDispatchmethodToSelect ($keyMode = 'id')
	{
		$Data = $this->getDispatchmethodAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key[$keyMode]] = $key['name'];
		}
		return $tmp;
	}

	public function updateDispatchmethodPaymentmethod ($array, $id)
	{
		DbTracker::deleteRows('dispatchmethodpaymentmethod', 'dispatchmethodid', $id);
		
		if (! empty($array)){
			foreach ($array as $value){
				$sql = 'INSERT INTO dispatchmethodpaymentmethod (dispatchmethodid, paymentmethodid)
							VALUES (:dispatchmethodid, :paymentmethodid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('dispatchmethodid', $id);
				$stmt->bindValue('paymentmethodid', $value);
				
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}
	}

	public function editDispatchmethod ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->updateDispatchmethod($Data, $id);
			$this->updateDispatchmethodPaymentmethod($Data['paymentmethodname'], $id);
			if ($Data['type'] == 1){
			    DbTracker::deleteRows('dispatchmethodweight', 'dispatchmethodid', $id);
				$this->updateDispatchmethodPrice($Data, $id);
			}
			if ($Data['type'] == 2){
			    DbTracker::deleteRows('dispatchmethodprice', 'dispatchmethodid', $id);
				$this->updateDispatchmethodWeight($Data, $id);
			}
			$this->updateDispatchmethodView($Data, $id);
			$this->updateDispatchmethodPhoto($Data, $id);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_DISPATCHMETHOD_EDIT'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function updateDispatchmethodView ($array, $id)
	{
		DbTracker::deleteRows('dispatchmethodview', 'dispatchmethodid', $id);
		
		if (! empty($array['view']) && is_array($array['view'])){
			foreach ($array['view'] as $value){
				$sql = 'INSERT INTO dispatchmethodview (viewid, dispatchmethodid)
							VALUES (:viewid, :dispatchmethodid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('dispatchmethodid', $id);
				$stmt->bindValue('viewid', $value);
				
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}
	}

	public function updateDispatchmethodPrice ($Data, $id)
	{
		DbTracker::deleteRows('dispatchmethodprice', 'dispatchmethodid', $id);
		
		foreach ($Data['table']['ranges'] as $key => $value){
			$sql = 'INSERT INTO dispatchmethodprice (dispatchmethodid, `from`, `to`, dispatchmethodcost, vat)
						VALUES (:dispatchmethodid, :from, :to, :dispatchmethodcost, :vat)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('dispatchmethodid', $id);
			if (isset($value['min'])){
				$stmt->bindValue('from', $value['min']);
			}
			else{
				$stmt->bindValue('from', 0.00);
			}
			if (isset($value['max'])){
				$stmt->bindValue('to', $value['max']);
			}
			else{
				$stmt->bindValue('to', 0.00);
			}
			if ($Data['table']['vat'] > 0 && isset($Data['table']['use_vat'])){
				$stmt->bindValue('vat', $Data['table']['vat']);
				$stmt->bindValue('dispatchmethodcost', $value['price']);
			}
			else{
				$stmt->bindValue('vat', NULL);
				$stmt->bindValue('dispatchmethodcost', $value['price']);
			}
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
		return $Data;
	}

	public function updateDispatchmethodWeight ($Data, $id)
	{
		DbTracker::deleteRows('dispatchmethodweight', 'dispatchmethodid', $id);
		
		foreach ($Data['tableweight']['ranges'] as $key => $value){
			$sql = 'INSERT INTO dispatchmethodweight (dispatchmethodid, `from`, `to`, cost, vat)
						VALUES (:dispatchmethodid, :from, :to, :cost,:vat)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('dispatchmethodid', $id);
			if (isset($value['min'])){
				$stmt->bindValue('from', $value['min']);
			}
			else{
				$stmt->bindValue('from', 0.00);
			}
			if (isset($value['max'])){
				$stmt->bindValue('to', $value['max']);
			}
			else{
				$stmt->bindValue('to', 0.00);
			}
			if ($Data['tableweight']['vat'] > 0 && isset($Data['tableweight']['use_vat'])){
				$stmt->bindValue('vat', $Data['tableweight']['vat']);
				$stmt->bindValue('cost', $value['price']);
			}
			else{
				$stmt->bindValue('vat', NULL);
				$stmt->bindValue('cost', $value['price']);
			}
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
		return $Data;
	}

	public function updateDispatchmethod ($Data, $id)
	{
		try{
			$sql = 'UPDATE dispatchmethod SET 
						name=:name, 
						description=:description,
						type = :type, 
						maximumweight = :maximumweight, 
						freedelivery = :freedelivery,
						countryids = :countryids,
						currencyid = :currencyid
					WHERE iddispatchmethod = :id';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $Data['name']);
			$stmt->bindValue('description', $Data['description']);
			$stmt->bindValue('type', $Data['type']);
			if ($Data['maximumweight'] != NULL){
				$stmt->bindValue('maximumweight', $Data['maximumweight']);
			}
			else{
				$stmt->bindValue('maximumweight', NULL);
			}
			if ($Data['freedelivery'] != NULL){
				$stmt->bindValue('freedelivery', $Data['freedelivery']);
			}
			else{
				$stmt->bindValue('freedelivery', NULL);
			}
			if (isset($Data['countryids']) && ! empty($Data['countryids'])){
				$stmt->bindValue('countryids', implode(',', $Data['countryids']));
			}
			else{
				$stmt->bindValue('countryids', '');
			}
			$stmt->bindValue('currencyid', $Data['currencyid']);
			$stmt->bindValue('id', $id);
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_DISPATCHMETHOD_EDIT'), 10, $e->getMessage());
		}
	}

	public function updateDispatchmethodPhoto ($Data, $id)
	{
		if (isset($Data['photo']['unmodified']) && $Data['photo']['unmodified']){
			return;
		}
		try{
			$sql = 'UPDATE dispatchmethod SET 
							photo = :photo
						WHERE iddispatchmethod = :id';
			$stmt = Db::getInstance()->prepare($sql);
			if (isset($Data['photo'][0])){
				$stmt->bindValue('photo', $Data['photo'][0]);
			}
			else{
				$stmt->bindValue('photo');
			}
			$stmt->bindValue('id', $id);
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_DISPATCHMETHOD_EDIT'), 10, $e->getMessage());
		}
	}

	public function getDispatchmethodView ($id)
	{
		$sql = "SELECT 
					iddispatchmethod AS id, 
					name, 
					description, 
					photo, 
					type, 
					maximumweight, 
					freedelivery,
					countryids,
					currencyid
				FROM dispatchmethod
				LEFT JOIN dispatchmethodview DV ON DV.dispatchmethodid = iddispatchmethod
				WHERE iddispatchmethod = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$countryids = Array();
			if ($rs['countryids'] != ''){
				$countryids = explode(',', $rs['countryids']);
			}
			$Data = Array(
				'name' => $rs['name'],
				'description' => $rs['description'],
				'type' => $rs['type'],
				'currencyid' => $rs['currencyid'],
				'maximumweight' => $rs['maximumweight'],
				'freedelivery' => $rs['freedelivery'],
				'paymentmethods' => $this->DispatchmethodPaymentmethodIds($id),
				'view' => $this->DispatchmethodView($id),
				'countryids' => $countryids
			);
			$Data['photo'] = $rs['photo'];
		}
		return $Data;
	}

	public function DispatchmethodView ($id)
	{
		$sql = "SELECT viewid
					FROM dispatchmethodview
					WHERE dispatchmethodid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['viewid'];
		}
		return $Data;
	}

	public function checkVat ($id)
	{
		$sql = "SELECT vat
					FROM dispatchmethodprice
					WHERE dispatchmethodid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'vat' => $rs['vat']
			);
		}
		if ($Data['vat'] > 0){
			return true;
		}
		else{
			return false;
		}
	}

	public function getDispatchmethodForOrder ($id)
	{
		$sql = 'SELECT type FROM dispatchmethod WHERE iddispatchmethod = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$type = $rs['type'];
		}
		if ($type == 1){
			$method = $this->getDispatchmethodPrice($id);
		}
		else{
			$method = $this->getDispatchmethodWeight($id);
		}
		if (isset($method['use_vat']) && $method['use_vat'] == 1 && $method['vat'] > 0){
			$vatData = App::getModel('vat')->getVATAllForRangeEditor();
			$vatValue = $vatData[$method['vat']];
		}
		else{
			$vatValue = 0;
		}
		return $vatValue;
	}

	public function getDispatchmethodPrice ($id)
	{
		$sql = 'SELECT iddispatchmethodprice as id, dispatchmethodcost, `from`, `to`, vat 
					FROM dispatchmethodprice
					WHERE dispatchmethodid=:id';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data['ranges'][] = Array(
				'min' => $rs['from'],
				'max' => $rs['to'],
				'price' => $rs['dispatchmethodcost']
			);
			if ($rs['vat'] > 0){
				$Data['vat'] = $rs['vat'];
				$Data['use_vat'] = 1;
			}
		}
		return $Data;
	}

	public function getDispatchmethodWeight ($id)
	{
		$sql = 'SELECT cost, `from`, `to`,vat
					FROM dispatchmethodweight
					WHERE dispatchmethodid=:id';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data['ranges'][] = Array(
				'min' => $rs['from'],
				'max' => $rs['to'],
				'price' => $rs['cost']
			);
			if ($rs['vat'] > 0){
				$Data['vat'] = $rs['vat'];
				$Data['use_vat'] = 1;
			}
		}
		return $Data;
	}

	public function DispatchmethodPaymentmethod ($id)
	{
		$sql = 'SELECT P.idpaymentmethod AS id, P.name AS paymentmethodname
					FROM dispatchmethodpaymentmethod DP
					LEFT JOIN paymentmethod P ON DP.paymentmethodid = P.idpaymentmethod
					WHERE DP.dispatchmethodid=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'paymentmethodname' => $rs['paymentmethodname'],
				'id' => $rs['id']
			);
		}
		return $Data;
	}

	public function DispatchmethodPaymentmethodIds ($id)
	{
		$Data = $this->DispatchmethodPaymentmethod($id);
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[] = $key['id'];
		}
		return $tmp;
	}

	public function addDispatchmethod ($Data)
	{
		$sql = 'INSERT INTO `dispatchmethod` (name,currencyid, description,type,maximumweight,freedelivery, photo,countryids) VALUES (:name,:currencyid, :description,:type,:maximumweight,:freedelivery, :photo,:countryids)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);
		$stmt->bindValue('description', $Data['description']);
		
		$stmt->bindValue('currencyid', $Data['currencyid']);
		if (isset($Data['photo'][0])){
			$stmt->bindValue('photo', $Data['photo'][0]);
		}
		else{
			$stmt->bindValue('photo', NULL);
		}
		$stmt->bindValue('type', $Data['type']);
		if ($Data['maximumweight'] != NULL){
			$stmt->bindValue('maximumweight', $Data['maximumweight']);
		}
		else{
			$stmt->bindValue('maximumweight', NULL);
		}
		if ($Data['freedelivery'] != NULL){
			$stmt->bindValue('freedelivery', $Data['freedelivery']);
		}
		else{
			$stmt->bindValue('freedelivery', NULL);
		}
		if (isset($Data['countryids']) && ! empty($Data['countryids'])){
			$stmt->bindValue('countryids', implode(',', $Data['countryids']));
		}
		else{
			$stmt->bindValue('countryids', '');
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_DISPATCHMETHOD_ADD'), 11, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	protected function addPaymentmethodToDispatchmethod ($array, $Dispatchmethodid)
	{
		foreach ($array as $value){
			$sql = 'INSERT INTO dispatchmethodpaymentmethod (dispatchmethodid, paymentmethodid)
						VALUES (:dispatchmethodid, :paymentmethodid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('dispatchmethodid', $Dispatchmethodid);
			$stmt->bindValue('paymentmethodid', $value);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function addNewDispatchmethod ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newDispatchmethodid = $this->addDispatchmethod($Data);
			if (is_array($Data['paymentmethodname']) && ! empty($Data['paymentmethodname'])){
				$this->addPaymentmethodToDispatchmethod($Data['paymentmethodname'], $newDispatchmethodid);
			}
			$this->addDispatchmethodPrice($Data['table'], $newDispatchmethodid);
			$this->addDispatchmethodWeight($Data['tableweight'], $newDispatchmethodid);
			if (is_array($Data['view']) && ! empty($Data['view'])){
				$this->addDispatchmethodView($Data, $newDispatchmethodid);
			}
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_DISPATCHMETHOD_ADD'), 11, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	protected function addDispatchmethodView ($Data, $id)
	{
		foreach ($Data['view'] as $key => $val){
			$sql = 'INSERT INTO dispatchmethodview (viewid, dispatchmethodid)
						VALUES (:viewid, :dispatchmethodid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('dispatchmethodid', $id);
			$stmt->bindValue('viewid', $val);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function addDispatchmethodPrice ($array, $dispatchmethodid)
	{
		foreach ($array['ranges'] as $key => $value){
			$sql = 'INSERT INTO dispatchmethodprice (dispatchmethodid, `from`, `to`, dispatchmethodcost, vat)
						VALUES (:dispatchmethodid, :from, :to, :dispatchmethodcost, :vat)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('dispatchmethodid', $dispatchmethodid);
			if (isset($value['min'])){
				$stmt->bindValue('from', $value['min']);
			}
			else{
				$stmt->bindValue('from', 0.00);
			}
			if (isset($value['max'])){
				$stmt->bindValue('to', $value['max']);
			}
			else{
				$stmt->bindValue('to', 0.00);
			}
			if ($array['vat'] > 0 && isset($array['use_vat'])){
				$stmt->bindValue('vat', $array['vat']);
				$stmt->bindValue('dispatchmethodcost', $value['price']);
			}
			else{
				$stmt->bindValue('vat', NULL);
				$stmt->bindValue('dispatchmethodcost', $value['price']);
			}
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function addDispatchmethodWeight ($array, $dispatchmethodid)
	{
		foreach ($array['ranges'] as $key => $value){
			$sql = 'INSERT INTO dispatchmethodweight (dispatchmethodid, `from`, `to`, cost)
						VALUES (:dispatchmethodid, :from, :to, :cost)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('dispatchmethodid', $dispatchmethodid);
			if (isset($value['min'])){
				$stmt->bindValue('from', $value['min']);
			}
			else{
				$stmt->bindValue('from', 0.00);
			}
			if (isset($value['max'])){
				$stmt->bindValue('to', $value['max']);
			}
			else{
				$stmt->bindValue('to', 0.00);
			}
			$stmt->bindValue('cost', $value['price']);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function doAJAXUpdateMethod ($request)
	{
		$sql = 'UPDATE dispatchmethod SET
					hierarchy = :hierarchy
				WHERE iddispatchmethod = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $request['id']);
		$stmt->bindValue('hierarchy', $request['hierarchy']);
		$stmt->execute();
		return true;
	}
}