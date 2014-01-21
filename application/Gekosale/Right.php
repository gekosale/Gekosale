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
 * $Id: right.class.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Exception;

class Right
{
	
	protected $_RIGHTS = Array(
		'index' => 1,
		'add' => 2,
		'edit' => 4,
		'delete' => 8,
		'view' => 16,
		'duplicate' => 32,
		'confirm' => 64
	);
	protected $_FRONTEND_ADDONS = Array(
		'accept' => 0,
		'cancel' => 1,
		'confirm' => 2,
		'notify' => 3,
		'redirect' => 4
	);
	protected $registry;
	
	public $_CONTROLLERRIGHTS = Array();

	public function __construct (ContainerInterface $container)
	{
		$this->container = $container;
	}

	public function getRights ()
	{
		return $this->_RIGHTS;
	}

	public function getRightsToSmarty ()
	{
		foreach ($this->_RIGHTS as $key => $value){
			$Data[] = Array(
				'name' => $key,
				'value' => $value
			);
		}
		return $Data;
	}

	public function checkMenuPermission ($name, $action, $layer)
	{
		if (! isset($this->_RIGHTS[$action])){
			$action = 'index';
		}
		if (($permission = $this->getControllerRightByName($name, $layer)) !== false){
			return $permission;
		}
		return $this->checkControllerRightMenu($name, $action, $layer);
	}

	public function checkPermission ($name, $action, $layer)
	{
		if (! isset($this->_RIGHTS[$action])){
			$action = 'index';
		}
		if (($permission = $this->getControllerRightByName($name, $layer)) !== false){
			return $permission;
		}
		return $this->checkControllerRight($name, $action, $layer);
	}

	public function checkPermissionBool ($name, $action, $layer)
	{
		if (! isset($this->_RIGHTS[$action])){
			return false;
		}
		if ($this->checkPermission($name, $action, $layer) & $this->_RIGHTS[$action] == $this->_RIGHTS[$action]){
			return true;
		}
		return false;
	}

	public function getControllerRightByName ($name, $layer)
	{
		if (isset($this->_CONTROLLERRIGHTS[$name][$layer])){
			return $this->_CONTROLLERRIGHTS[$name][$layer];
		}
		return false;
	}

	protected function checkControllerRight ($name, $action, $layer)
	{
		$globaluser = App::getContainer()->get('session')->getActiveUserIsGlobal();
		
		if ($globaluser == 0){
			if (Helper::getViewId() > 0){
				$sql = 'SELECT permission
								FROM  `right` R
								LEFT JOIN controller C ON C.idcontroller = R.controllerid
								LEFT JOIN usergroupview UGV ON UGV.groupid = R.groupid
								WHERE C.name = :name
								AND C.enable = 1
								AND UGV.userid = :userid
								AND C.mode = 1
								AND UGV.viewid = :viewid';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('name', $name);
				$stmt->bindValue('userid', App::getContainer()->get('session')->getActiveUserid());
				$stmt->bindValue('viewid', Helper::getViewId());
			}
			else{
				$sql = 'SELECT permission
								FROM  `right` R
								LEFT JOIN controller C ON C.idcontroller = R.controllerid
								LEFT JOIN usergroupview UGV ON UGV.groupid = R.groupid
								WHERE C.name = :name
								AND C.enable = 1
								AND UGV.userid = :userid
								AND C.mode = 1
								AND UGV.viewid IN (' . implode(',', Helper::getViewIds()) . ')';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('name', $name);
				$stmt->bindValue('userid', App::getContainer()->get('session')->getActiveUserid());
			}
		}
		else{
			$sql = 'SELECT permission
						FROM  `right` R
						LEFT JOIN controller C ON C.idcontroller = R.controllerid
						LEFT JOIN usergroup UG ON UG.groupid = R.groupid
						WHERE C.name = :name
						AND C.enable = 1
						AND UG.userid = :userid
						AND C.mode = 1
						';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $name);
			$stmt->bindValue('userid', App::getContainer()->get('session')->getActiveUserid());
		}
		
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			if (($rs['permission'] & $this->_RIGHTS[$action]) == $this->_RIGHTS[$action]){
				$this->_CONTROLLERRIGHTS[$name][(int) $layer] = $rs['permission'];
				return $rs['permission'];
			}
			throw new Exception('No privileges for ' . $name);
		}
		throw new Exception('No privileges for ' . $name);
	}

	protected function checkControllerRightMenu ($name, $action, $layer)
	{
		
		$globaluser = App::getContainer()->get('session')->getActiveUserIsGlobal();
		
		if ($globaluser == 0){
			if (Helper::getViewId() > 0){
				$sql = 'SELECT permission
								FROM  `right` R
								LEFT JOIN controller C ON C.idcontroller = R.controllerid
								LEFT JOIN usergroupview UGV ON UGV.groupid = R.groupid
								WHERE C.name = :name
								AND C.enable = 1
								AND UGV.userid = :userid
								AND C.mode = 1
								AND UGV.viewid = :viewid';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('name', $name);
				$stmt->bindValue('userid', App::getContainer()->get('session')->getActiveUserid());
				$stmt->bindValue('viewid', Helper::getViewId());
			
			}
			else{
				$sql = 'SELECT permission
								FROM  `right` R
								LEFT JOIN controller C ON C.idcontroller = R.controllerid
								LEFT JOIN usergroupview UGV ON UGV.groupid = R.groupid
								WHERE C.name = :name
								AND C.enable = 1
								AND UGV.userid = :userid
								AND C.mode = 1
								AND UGV.viewid IN (' . implode(',', Helper::getViewIds()) . ')';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('name', $name);
				$stmt->bindValue('userid', App::getContainer()->get('session')->getActiveUserid());
			}
		}
		else{
			$sql = 'SELECT permission FROM `right` R
	    			LEFT JOIN controller C ON C.idcontroller = R.controllerid
	    			WHERE name = :name AND `enable` = 1
	    			AND groupid = :groupid AND mode = 1';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $name);
			$stmt->bindValue('groupid', App::getContainer()->get('session')->getActiveUserGroupid());
		}
		
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			if (($rs['permission'] & $this->_RIGHTS[$action]) == $this->_RIGHTS[$action]){
				$this->_CONTROLLERRIGHTS[$name][(int) $layer] = $rs['permission'];
				return $rs['permission'];
			}
			return false;
		}
		return false;
	}

	public function checkDeletePermission ($modulename)
	{
		try{
			$this->checkControllerRight($modulename, 'delete', Helper::getViewId());
		}
		catch (Exception $e){
			return false;
		}
	}

	public function flushPermission ()
	{
		$this->_CONTROLLERRIGHTS = Array();
	}

	public function getAllRights ()
	{
		return array_merge($this->_RIGHTS, $this->_FRONTEND_ADDONS);
	}
}
