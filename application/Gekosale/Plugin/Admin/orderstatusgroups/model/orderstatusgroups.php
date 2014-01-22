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
 * $Id: orderstatusgroups.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale\Plugin;

class OrderStatusGroupsModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('orderstatusgroups', Array(
			'idorderstatusgroups' => Array(
				'source' => 'OSG.idorderstatusgroups'
			),
			'name' => Array(
				'source' => 'OSGT.name',
				'prepareForAutosuggest' => true
			),
			'colour' => Array(
				'source' => 'OSG.colour',
			),
			'adddate' => Array(
				'source' => 'adddate'
			)
		));
		
		$datagrid->setFrom('
			orderstatusgroups OSG
			LEFT JOIN orderstatusgroupstranslation OSGT ON OSG.idorderstatusgroups = OSGT.orderstatusgroupsid AND OSGT.languageid = :languageid
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

	public function getOrderStatusGroupsForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteOrderStatusGroups ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteOrderStatusGroups'
		), $this->getName());
	}

	public function deleteOrderStatusGroups ($id)
	{
		DbTracker::deleteRows('orderstatusgroups', 'idorderstatusgroups', $id);
	}

	public function getOrderStatusGroupsView ($id)
	{
		$sql = "SELECT 
					idorderstatusgroups AS id,
					colour
				FROM orderstatusgroups 
				WHERE idorderstatusgroups = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'language' => $this->getOrderStatusGroupsTranslation($id),
				'id' => $rs['id'],
				'colour' => $rs['colour'],
				'orderstatus' => $this->orderStatusOrderStatusGroupsIds($id)
			);
		}
		else{
			throw new CoreException($this->trans('ERR_ORDER_STATUS_GROUPS_NO_EXIST'));
		}
		return $Data;
	}

	public function getOrderStatusGroupsTranslation ($id)
	{
		$sql = "SELECT name, languageid
					FROM orderstatusgroupstranslation
					WHERE orderstatusgroupsid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['languageid']] = Array(
				'name' => $rs['name']
			);
		}
		return $Data;
	}

	public function orderStatusOrderStatusGroups ($id)
	{
		$sql = 'SELECT OS.idorderstatus AS id
					FROM orderstatusorderstatusgroups OSG
					LEFT JOIN orderstatus OS ON OSG.orderstatusid = OS.idorderstatus
					WHERE OSG.orderstatusgroupsid=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id']
			);
		}
		return $Data;
	}

	public function orderStatusOrderStatusGroupsIds ($id)
	{
		$Data = $this->orderStatusOrderStatusGroups($id);
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[] = $key['id'];
		}
		return $tmp;
	}

	public function addNewOrderStatusGroups ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newOrderStatusGroupId = $this->addOrderStatusGroups($Data);
			$this->addOrderStatusOrderStatusGroups($Data['orderstatus'], $newOrderStatusGroupId);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_ORDER_STATUS_GROUPS_ADD'), 11, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function addOrderStatusGroups ($Data)
	{
		$sql = 'INSERT INTO orderstatusgroups (colour) VALUES (:colour)';
		$stmt = Db::getInstance()->prepare($sql);
		if (strlen($Data['colour']['start']) == 6){
			$stmt->bindValue('colour', $Data['colour']['start']);
		}
		else{
			$stmt->bindValue('colour', NULL);
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException('ERR_ORDER_STATUS_GROUP_ADD', 15, $e->getMessage());
		}
		
		$orderstatusgroupsid = Db::getInstance()->lastInsertId();
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO orderstatusgroupstranslation (orderstatusgroupsid,name, languageid)
						VALUES (:orderstatusgroupsid,:name, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('orderstatusgroupsid', $orderstatusgroupsid);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_ORDER_STATUS_GROUP_ADD'), 15, $e->getMessage());
			}
		}
		
		return $orderstatusgroupsid;
	}

	protected function addOrderStatusOrderStatusGroups ($orderstatusarray, $newOrderStatusGroupId)
	{
		foreach ($orderstatusarray as $value){
			$sql = 'INSERT INTO orderstatusorderstatusgroups (orderstatusid, orderstatusgroupsid)
					VALUES (:orderstatusid, :orderstatusgroupsid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('orderstatusgroupsid', $newOrderStatusGroupId);
			$stmt->bindValue('orderstatusid', $value);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function editOrderStatusGroups ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->updateOrderStatusGroups($Data, $id);
			$this->updateOrderStatusOrderStatusGroups($Data['orderstatus'], $id);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_ORDER_STATUS_GROUPS_EDIT'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function updateOrderStatusGroups ($Data, $id)
	{
		$sql = 'UPDATE orderstatusgroups SET colour = :colour WHERE idorderstatusgroups = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		if (strlen($Data['colour']['start']) == 6){
			$stmt->bindValue('colour', $Data['colour']['start']);
		}
		else{
			$stmt->bindValue('colour', NULL);
		}
		$stmt->execute();
		
		DbTracker::deleteRows('orderstatusgroupstranslation', 'orderstatusgroupsid', $id);
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO orderstatusgroupstranslation (orderstatusgroupsid,name, languageid)
					VALUES (:orderstatusgroupsid,:name, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('orderstatusgroupsid', $id);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_ORDER_STATUS_GROUP_ADD'), 15, $e->getMessage());
			}
		}
		
		return true;
	}

	public function updateOrderStatusOrderStatusGroups ($array, $id)
	{
		
		foreach ($array as $value){
			
			DbTracker::deleteRows('orderstatusorderstatusgroups', 'orderstatusid', $value);
			
			$sql = 'INSERT INTO orderstatusorderstatusgroups (orderstatusid, orderstatusgroupsid)
					VALUES (:orderstatusid, :orderstatusgroupsid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('orderstatusgroupsid', $id);
			$stmt->bindValue('orderstatusid', $value);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function getOrderStatusGroupsAll ()
	{
		$sql = 'SELECT 
					OSG.idorderstatusgroups AS id,
					OSGT.name as name
				FROM orderstatusgroups OSG
				LEFT JOIN orderstatusgroupstranslation OSGT ON OSGT.orderstatusgroupsid = OSG.idorderstatusgroups AND OSGT.languageid = :languageid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $rs['name']
			);
		}
		return $Data;
	}

	public function getOrderStatusGroupsAllToSelect ()
	{
		$Data = $this->getOrderStatusGroupsAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return $tmp;
	}
}