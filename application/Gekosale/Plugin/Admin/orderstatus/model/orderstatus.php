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
 * $Id: orderstatus.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Plugin;

class OrderStatusModel extends Component\Model\Datagrid
{

	public function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('orderstatus', Array(
			'idorderstatus' => Array(
				'source' => 'O.idorderstatus'
			),
			'name' => Array(
				'source' => 'OST.name',
				'prepareForAutosuggest' => true
			),
			'groupname' => Array(
				'source' => 'OSGT.name',
				'prepareForSelect' => true
			),
			'adddate' => Array(
				'source' => 'O.adddate'
			),
			'def' => Array(
				'source' => 'O.default'
			)
		));
		
		$datagrid->setFrom('
			`orderstatus` O
			LEFT JOIN orderstatustranslation OST ON O.idorderstatus = OST.orderstatusid AND OST.languageid = :languageid
			LEFT JOIN orderstatusorderstatusgroups OS ON OS.orderstatusid = O.idorderstatus
			LEFT JOIN orderstatusgroups OSG ON OSG.idorderstatusgroups = OS.orderstatusgroupsid
			LEFT JOIN orderstatusgroupstranslation OSGT ON OSG.idorderstatusgroups = OSGT.orderstatusgroupsid AND OSGT.languageid = :languageid
		');
		
		$datagrid->setGroupBy('O.idorderstatus');
	}

	public function getNameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getOrderstatusForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteOrderstatus ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteOrderstatus'
		), $this->getName());
	}

	public function deleteOrderstatus ($id)
	{
		$sql = "SELECT COUNT(idorderhistory) as total FROM `orderhistory` WHERE orderstatusid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		
		$sql2 = "SELECT COUNT(idorder) as total FROM `order` WHERE orderstatusid = :id";
		$stmt2 = Db::getInstance()->prepare($sql2);
		$stmt2->bindValue('id', $id);
		$stmt2->execute();
		$rs2 = $stmt2->fetch();
		
		if ($rs['total'] == 0 && $rs2['total'] == 0){
			DbTracker::deleteRows('orderstatus', 'idorderstatus', $id);
		}
		else{
			return Array(
				'error' => $this->trans('ERR_ORDERSTATUS_USED_IN_ORDERS')
			);
		}
	}

	public function addNewOrderstatus ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newOrderStatusId = $this->addOrderStatus($Data);
			$this->addOrderStatusOrderStatusGroups($Data['orderstatusgroupsid'], $newOrderStatusId);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_ORDER_STATUS_ADD'), 11, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function addOrderStatus ($Data)
	{
		$sql = 'INSERT INTO orderstatus (adddate) VALUES (NOW())';
		$stmt = Db::getInstance()->prepare($sql);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_ORDERSTATUS_ADD'), 11, $e->getMessage());
		}
		
		$orderstatusid = Db::getInstance()->lastInsertId();
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO orderstatustranslation (orderstatusid, name,comment,smscomment, languageid)
						VALUES (:orderstatusid, :name,:comment,:smscomment, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('orderstatusid', $orderstatusid);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('comment', $Data['comment'][$key]);
			$stmt->bindValue('smscomment', $Data['smscomment'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_ORDERSTATUS_ADD'), 15, $e->getMessage());
			}
		}
		return $orderstatusid;
	}

	public function addOrderStatusOrderStatusGroups ($orderstatusgroupsid, $id)
	{
		$sql = 'INSERT INTO orderstatusorderstatusgroups (orderstatusgroupsid, orderstatusid) VALUES (:orderstatusgroupsid, :orderstatusid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('orderstatusgroupsid', $orderstatusgroupsid);
		$stmt->bindValue('orderstatusid', $id);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_ORDERSTATUS_ADD'), 11, $e->getMessage());
		}
		return true;
	}

	public function editOrderstatus ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->updateOrderstatus($Data, $id);
			$this->updateOrderStatusOrderStatusGroups($Data, $id);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_ORDER_STATUS_EDIT'), 125, $e->getMessage());
		}
		Db::getInstance()->commit();
		return true;
	}

	public function updateOrderstatus ($Data, $id)
	{
		DbTracker::deleteRows('orderstatustranslation', 'orderstatusid', $id);
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO orderstatustranslation (orderstatusid,name,comment,smscomment, languageid)
						VALUES (:orderstatusid,:name,:comment,:smscomment, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('orderstatusid', $id);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('comment', $Data['comment'][$key]);
			$stmt->bindValue('smscomment', $Data['smscomment'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_ORDERSTATUS_EDIT'), 129, $e->getMessage());
			}
		}
		return true;
	}

	public function updateOrderStatusOrderStatusGroups ($Data, $id)
	{
		DbTracker::deleteRows('orderstatusorderstatusgroups', 'orderstatusid', $id);
		
		$sql = 'INSERT INTO orderstatusorderstatusgroups (orderstatusgroupsid, orderstatusid) VALUES (:orderstatusgroupsid, :id)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('orderstatusgroupsid', $Data['orderstatusgroupsid']);
		$stmt->bindValue('id', $id);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException('ERR_ORDER_STATUS_UPDATE', 15, $e->getMessage());
		}
	}

	public function getOrderstatusView ($id)
	{
		$sql = "SELECT idorderstatus AS id, orderstatusgroupsid FROM orderstatus
					LEFT JOIN orderstatusorderstatusgroups OSG ON OSG.orderstatusid = idorderstatus
					WHERE idorderstatus = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'language' => $this->getOrderStatusTranslation($id),
				'id' => $rs['id'],
				'orderstatusgroupsid' => $rs['orderstatusgroupsid']
			);
		}
		else{
			throw new CoreException($this->trans('ERR_ORDERSTATUS_NO_EXIST'));
		}
		return $Data;
	}

	public function getOrderStatusTranslation ($id)
	{
		$sql = "SELECT name,comment,smscomment,languageid
					FROM orderstatustranslation
					WHERE orderstatusid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['languageid']] = Array(
				'name' => $rs['name'],
				'comment' => $rs['comment'],
				'smscomment' => $rs['smscomment'],
			);
		}
		return $Data;
	}

	public function getOrderStatusAll ()
	{
		$sql = 'SELECT 
					OST.orderstatusid, 
					OST.name 
				FROM `orderstatustranslation` OST 
				LEFT JOIN orderstatus OS ON OST.orderstatusid = OS.idorderstatus
				WHERE OST.languageid = :id
				ORDER BY OST.name ASC';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', App::getContainer()->get('session')->getActiveLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['orderstatusid'],
				'name' => $rs['name']
			);
		}
		return $Data;
	}

	public function getOrderStatusToSelect ($emptyValue = null)
	{
		$Data = $this->getOrderStatusAll();
		$tmp = Array();
        if($emptyValue)
            $tmp[''] = $emptyValue;
        
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return $tmp;
	}

	public function doAJAXDefault ($datagridId, $id)
	{
		try{
			$this->setDefault($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_SET_DEFAULT_ORDER_STATUS')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function getDefaultComment ($id)
	{
		$Data = $this->getOrderStatusTranslation($id);
		return isset($Data[Helper::getLanguageId()]['comment']) ? $Data[Helper::getLanguageId()]['comment'] : '';
	}
	
	public function getDefaultSmsComment ($id)
	{
		$Data = $this->getOrderStatusTranslation($id);
		return isset($Data[Helper::getLanguageId()]['smscomment']) ? $Data[Helper::getLanguageId()]['smscomment'] : '';
	}

	public function setDefault ($id)
	{
		$sql = 'UPDATE orderstatus SET `default`= 1 
					WHERE idorderstatus = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
			
			$sql2 = 'UPDATE orderstatus SET `default`=0 
						WHERE idorderstatus <> :id';
			$stmt2 = Db::getInstance()->prepare($sql2);
			$stmt2->bindValue('id', $id);
			$stmt2->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}
}		