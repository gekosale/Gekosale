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
 * $Id: groups.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;

class GroupsModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('clientgroup', Array(
			'idgroup' => Array(
				'source' => 'G.idgroup'
			),
			'name' => Array(
				'source' => 'G.name',
				'prepareForAutosuggest' => true
			),
			'usercount' => Array(
				'source' => 'COUNT(DISTINCT UG.userid)',
				'filter' => 'having'
			),
			'adddate' => Array(
				'source' => 'G.adddate'
			),
		));
		
		$datagrid->setFrom('
			`group` G
			LEFT JOIN `usergroup` UG ON UG.groupid = G.idgroup
		');
		
		$datagrid->setGroupBy('
			G.idgroup
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

	public function getGroupsForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteGroups ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteGroup'
		), $this->getName());
	}

	public function deleteGroup ($id)
	{
		DbTracker::deleteRows('group', 'idgroup', $id);
	}

	public function getGroupsView ($id)
	{
		$sql = "SELECT idgroup AS id, name FROM `group` 
				WHERE idgroup = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'id' => $rs['id'],
				'name' => $rs['name']
			);
		}
		return $Data;
	}

	public function getGroupsAll ()
	{
		$sql = 'SELECT idgroup AS id, name FROM `group`';
		$stmt = Db::getInstance()->prepare($sql);
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

	public function getGroupsAllToSelect ()
	{
		$Data = $this->getGroupsAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return $tmp;
	}

	public function getUsersAll ($id)
	{
		$sql = 'SELECT 
					UD.firstname, 
					UD.surname 
				FROM userdata UD 
				LEFT JOIN usergroup UG ON UG.userid = UD.userid 
				WHERE UG.groupid=:id';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'firstname' => $rs['firstname'],
				'surname' => $rs['surname']
			);
		}
		return $Data;
	}

	protected function setPermission ($Data)
	{
		$Perm = Array();
		foreach ($Data as $controller => $value){
			if (count($value) > 0 && is_array($value)){
				foreach ($value as $action => $permission){
					if (! isset($Perm[$controller])){
						$Perm[$controller] = 0;
					}
					$Perm[$controller] += $permission * $action;
				}
			}
		}
		
		return $Perm;
	}

	protected function updatePermission ($Data, $id)
	{
		if (! is_array($Data) || count($Data) == 0){
			return;
		}
		$current = $this->getGroupRightsById($id);
		
		
		$sql = 'DELETE FROM `right` WHERE controllerid NOT IN (' . implode(',', array_keys($Data)) . ')	AND groupid = :groupid AND storeid IS NULL';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('groupid', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		foreach ($Data as $key => $value){
			if (! array_key_exists($key, $current)){
				$sql = 'INSERT INTO `right` (controllerid, groupid, permission, storeid)
						VALUES (:controllerid, :groupid, :permission, :storeid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('controllerid', $key);
				$stmt->bindValue('groupid', $id);
				$stmt->bindValue('permission', $value);
				$stmt->bindValue('storeid', NULL);
				
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
			else{
				$sql = 'UPDATE `right` SET permission = :permission
						WHERE 
							controllerid = :controllerid AND 
							groupid = :groupid AND 
							storeid IS NULL';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('controllerid', $key);
				$stmt->bindValue('groupid', $id);
				$stmt->bindValue('permission', $value);
				
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}
	}

	public function editPermission ($Data, $id)
	{
		$this->editGroup($Data, $id);
		$this->updatePermission($this->setPermission($Data['rights_data']['rights']), $id);
		App::getContainer()->get('right')->flushPermission();
	}

	protected function editGroup ($Data, $id)
	{
		$sql = 'UPDATE `group` SET name=:name WHERE idgroup = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['basic_data']['name']);
		
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_GROUP_EDIT'), 13, $e->getMessage());
			return false;
		}
		return true;
	}

	public function getGroupRightsById ($id)
	{
		$sql = 'SELECT controllerid, permission FROM `right` WHERE groupid=:id AND storeid IS NULL';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['controllerid']] = $rs['permission'];
		}
		return $Data;
	}

	public function getStoreGroupRightsById ($id, $storeid)
	{
		$sql = 'SELECT controllerid, permission,storeid FROM `right` WHERE groupid=:id AND storeid = :storeid';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('storeid', $storeid);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['controllerid']] = $rs['permission'];
		}
		return $Data;
	}

	protected function addGroup ($Data)
	{
		$sql = 'INSERT INTO `group` (name) VALUES (:name)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['basic_data']['name']);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_GROUP_ADD'), 14, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function add ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$id = $this->addGroup($Data);
			$this->updatePermission($this->setPermission($Data['rights_data']['rights']), $id);
		}
		catch (Exception $e){
			throw new Exception($this->trans('ERR_GROUP_ADD_ERROR'), 3001, $e->getMessage());
		}
		
		Db::getInstance()->commit();
	}

	public function getFullPermission ()
	{
		$permissions = $this->getGroupRightsById($this->registry->core->getParam());
		$controllers = App::getModel('controller')->getControllerSimpleList();
		$Data = Array();
		foreach ($controllers as $con){
			$permission = 0;
			if (array_key_exists($con['id'], $permissions)){
				$permission = $permissions[$con['id']];
			}
			$Data[] = Array(
				'id' => $con['id'],
				'name' => $con['name'],
				'permission' => $permission
			);
		}
		return $Data;
	}

	public function getStorePermission ($storeid)
	{
		$permissions = $this->getStoreGroupRightsById($this->registry->core->getParam(), $storeid);
		$controllers = App::getModel('controller')->getControllerSimpleList();
		$Data = Array();
		foreach ($controllers as $con){
			$permission = 0;
			if (array_key_exists($con['id'], $permissions)){
				$permission = $permissions[$con['id']];
			}
			$Data[] = Array(
				'id' => $con['id'],
				'name' => $con['name'],
				'permission' => $permission
			);
		}
		return $Data;
	}

}