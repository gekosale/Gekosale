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
 * $Id: paymentmethod.php 619 2011-12-19 21:09:00Z gekosale $ 
 */
namespace Gekosale;

class PaymentmethodModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('paymentmethod', Array(
			'idpaymentmethod' => Array(
				'source' => 'P.idpaymentmethod'
			),
			'name' => Array(
				'source' => 'P.name',
				'prepareForAutosuggest' => true,
				'processLanguage' => true
			),
			'controller' => Array(
				'source' => 'P.controller',
				'prepareForAutosuggest' => true
			),
			'active' => Array(
				'source' => 'P.active'
			),
			'hierarchy' => Array(
				'source' => 'P.hierarchy'
			)
		));
		
		$datagrid->setFrom('
			paymentmethod P
			LEFT JOIN paymentmethodview PV ON PV.paymentmethodid = P.idpaymentmethod
		');
		
		$datagrid->setGroupBy('
			P.idpaymentmethod
		');
		
		$datagrid->setAdditionalWhere('
			IF(:viewid IS NOT NULL, PV.viewid = :viewid, 1)
		');
	}

	public function getNameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
	}

	public function getControllerForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('controller', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getPaymentmethodForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeletePaymentmethod ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deletePaymentmethod'
		), $this->getName());
	}

	public function deletePaymentmethod ($id)
	{
		$sql = "SELECT COUNT(idorder) as total FROM `order` WHERE paymentmethodid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs['total'] == 0){
			return DbTracker::deleteRows('paymentmethod', 'idpaymentmethod', $id);
		}
		else{
			return Array(
				'error' => $this->trans('ERR_PAYMENTMETHOD_USED_IN_ORDERS')
			);
		}
	}

	public function doAJAXEnablePaymentmethod ($datagridId, $id)
	{
		try{
			$this->enablePaymentmethod($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_ENABLE_USER')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function doAJAXDisablePaymentmethod ($datagridId, $id)
	{
		try{
			$this->disablePaymentmethod($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_DISABLE_USER')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function disablePaymentmethod ($id)
	{
		$sql = 'UPDATE paymentmethod SET active = 0 WHERE idpaymentmethod = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function enablePaymentmethod ($id)
	{
		$sql = 'UPDATE paymentmethod SET active = 1 WHERE idpaymentmethod = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function getPaymentmethodView ($id)
	{
		$sql = "SELECT name,controller FROM paymentmethod WHERE idpaymentmethod = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'name' => $rs['name'],
				'controller' => $rs['controller'],
				'dispatchmethod' => $this->DispatchmethodPaymentmethodIds($id),
				'view' => $this->PaymentmethodView($id)
			);
		}
		return $Data;
	}

	public function PaymentmethodView ($id)
	{
		$sql = "SELECT viewid
					FROM paymentmethodview
					WHERE paymentmethodid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['viewid'];
		}
		return $Data;
	}

	public function DispatchmethodPaymentmethod ($id)
	{
		$sql = 'SELECT DM.iddispatchmethod AS id, DM.name AS dispatchmethodname
					FROM dispatchmethodpaymentmethod DPM
					LEFT JOIN dispatchmethod DM ON DPM.dispatchmethodid = DM.iddispatchmethod
					WHERE DPM.paymentmethodid=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'dispatchmethodname' => $rs['dispatchmethodname'],
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

	public function getPaymentmethodAll ()
	{
		$sql = 'SELECT idpaymentmethod as id, name, controller FROM paymentmethod';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $this->trans($rs['name']),
				'controller' => $rs['controller']
			);
		}
		return $Data;
	}

	public function editPaymentmethod ($Data, $id, $model)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->updatePaymentmethod($Data, $id);
			$this->updateDispatchmethodPaymentmethod($Data['dispatchmethod'], $id);
			$this->updatePaymentmethodView($Data['view'], $id);
			Event::notify($this, 'admin.paymentmethod.model.save', Array(
				'id' => $id,
				'data' => $Data,
				'model' => $model
			));
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_DISPATCHMETHOD_EDIT'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function updatePaymentmethodView ($array, $id)
	{
		DbTracker::deleteRows('paymentmethodview', 'paymentmethodid', $id);
		
		if (is_array($array) && ! empty($array)){
			foreach ($array as $value){
				$sql = 'INSERT INTO paymentmethodview (viewid, paymentmethodid)
							VALUES (:viewid, :paymentmethodid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('paymentmethodid', $id);
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

	public function updatePaymentmethod ($Data, $id)
	{
		$sql = 'UPDATE paymentmethod SET name=:name WHERE idpaymentmethod = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('name', $Data['name']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			echo $e->getMessage();
			return false;
		}
		return true;
	}

	public function updateDispatchmethodPaymentmethod ($array, $id)
	{
		DbTracker::deleteRows('dispatchmethodpaymentmethod', 'paymentmethodid', $id);
		
		if (is_array($array) && ! empty($array)){
			foreach ($array as $value){
				$sql = 'INSERT INTO dispatchmethodpaymentmethod (dispatchmethodid, paymentmethodid)
							VALUES (:dispatchmethodid, :paymentmethodid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('paymentmethodid', $id);
				$stmt->bindValue('dispatchmethodid', $value);
				
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}
	}

	public function getPaymentmethodModelById ($id)
	{
		$sql = 'SELECT controller FROM paymentmethod WHERE idpaymentmethod = :idpaymentmethod';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idpaymentmethod', $id);
		$stmt->execute();
		$controller = null;
		while ($rs = $stmt->fetch()){
			$controller = $rs['controller'];
		}
		return $controller;
	}

	public function getPaymentmethodToSelect ()
	{
		$Data = $this->getPaymentmethodAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return $tmp;
	}

	protected function addPaymentmethodToDispatchmethod ($dispatchmethodarray, $Paymentmethodid)
	{
		foreach ($dispatchmethodarray as $value){
			$sql = 'INSERT INTO dispatchmethodpaymentmethod (dispatchmethodid, paymentmethodid)
						VALUES (:dispatchmethodid, :paymentmethodid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('paymentmethodid', $Paymentmethodid);
			$stmt->bindValue('dispatchmethodid', $value);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	protected function addPaymentmethodView ($array, $id)
	{
		foreach ($array as $value){
			$sql = 'INSERT INTO paymentmethodview (viewid, paymentmethodid)
						VALUES (:viewid, :paymentmethodid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('paymentmethodid', $id);
			$stmt->bindValue('viewid', $value);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function addNewPaymentmethod ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newPaymentmethodid = $this->addPaymentmethod($Data);
			if (is_array($Data['dispatchmethod']) && ! empty($Data['dispatchmethod'])){
				$this->addPaymentmethodToDispatchmethod($Data['dispatchmethod'], $newPaymentmethodid);
			}
			if (is_array($Data['view']) && ! empty($Data['view'])){
				$this->addPaymentmethodView($Data['view'], $newPaymentmethodid);
			}
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_DISPATCHMETHOD_ADD'), 11, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return $newPaymentmethodid;
	}

	public function addPaymentmethod ($Data)
	{
		$sql = 'INSERT INTO paymentmethod (name,controller) VALUES (:name,:controller)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);
		$stmt->bindValue('controller', $Data['controller']);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException('ERR_PAYMENTMETHOD_ADD', 15, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function getDispatchmethodPrice ($id)
	{
		$sql = 'SELECT idpaymentmethod as id, dispatchmethodcost, `from`, `to`, vat 
					FROM paymentmethod
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
			$Data['vat'] = $rs['vat'];
		}
		return $Data;
	}
	
	public function doAJAXUpdateMethod ($request)
	{
		$sql = 'UPDATE paymentmethod SET
					hierarchy = :hierarchy
				WHERE idpaymentmethod = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $request['id']);
		$stmt->bindValue('hierarchy', $request['hierarchy']);
		$stmt->execute();
		return true;
	}
}