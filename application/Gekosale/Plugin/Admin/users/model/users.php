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
 * $Id: users.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Plugin;

class usersModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('userdata', Array(
			'iduser' => Array(
				'source' => 'U.iduser'
			),
			'active' => Array(
				'source' => 'U.active'
			),
			'firstname' => Array(
				'source' => 'UD.firstname',
				'prepareForAutosuggest' => true
			),
			'surname' => Array(
				'source' => 'UD.surname',
				'prepareForAutosuggest' => true
			),
			'email' => Array(
				'source' => 'UD.email',
				'prepareForAutosuggest' => true
			),
			'groupnames' => Array(
				'source' => 'IF(U.globaluser = 1,G.name,G2.name)',
				'prepareForSelect' => true
			),
			'groupname' => Array(
				'source' => 'GROUP_CONCAT(SUBSTRING(CONCAT(\' \', IF(U.globaluser = 1,G.name,G2.name)), 1))',
				'filter' => 'having'
			),
			'adddate' => Array(
				'source' => 'U.adddate'
			),
		));
		
		$globaluser = App::getContainer()->get('session')->getActiveUserIsGlobal();
		
		if ($globaluser == 0){
			$datagrid->setFrom('
				`user` U
				LEFT JOIN `userdata` UD ON UD.userid = U.iduser
				LEFT JOIN `usergroup` UG ON UG.userid = U.iduser
				LEFT JOIN `group` G ON G.idgroup = UG.groupid
				INNER JOIN usergroupview UGV ON U.iduser = UGV.userid AND UGV.viewid IN (:views)
				LEFT JOIN `group` G2 ON G2.idgroup = UGV.groupid
			');
		}
		else{
			$datagrid->setFrom('
				`user` U
				LEFT JOIN `userdata` UD ON UD.userid = U.iduser
				LEFT JOIN `usergroup` UG ON UG.userid = U.iduser
				LEFT JOIN usergroupview UGV ON U.iduser = UGV.userid
				LEFT JOIN `group` G ON G.idgroup = UG.groupid
				LEFT JOIN `group` G2 ON G2.idgroup = UGV.groupid
			');
		}
		$datagrid->setGroupBy('
				U.iduser
		');
	}

	public function getUsers ()
	{
		$sql = 'SELECT userid AS id, firstname, surname, email FROM userdata ORDER BY surname, firstname';
		$stmt = Db::getInstance()->prepare($sql);
		return $stmt->fetchAll();
	}

	public function getUsersCount ()
	{
	
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getUsersForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteUser ($id = NULL, $datagridName = 'list-users')
	{
		return $this->getDatagrid()->deleteRow($datagridName, $id, Array(
			$this,
			'deleteUser'
		), $this->getName());
	}

	public function deleteUser ($id)
	{
		DbTracker::deleteRows('user', 'iduser', $id);
	}

	public function doAJAXEnableUser ($datagridId, $id)
	{
		try{
			$this->enableUser($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_ENABLE_USER')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function doAJAXDisableUser ($datagridId, $id)
	{
		try{
			$this->disableUser($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_DISABLE_USER')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function disableUser ($id)
	{
		if ($id == App::getContainer()->get('session')->getActiveUserid()){
			throw new Exception($this->trans('ERR_CAN_NOT_DISABLE_YOURSELF'));
		}
		$sql = 'UPDATE user SET
					active = 0
					WHERE iduser = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function enableUser ($id)
	{
		if ($id == App::getContainer()->get('session')->getActiveUserid()){
			throw new Exception($this->trans('ERR_CAN_NOT_ENABLE_YOURSELF'));
		}
		$sql = 'UPDATE user SET
					active = 1
					WHERE iduser = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function getUsersToSelect ()
	{
		$rs = $this->registry->db->executeQuery('SELECT userid AS id, firstname, surname FROM userdata
													ORDER BY surname, firstname');
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = $rs['firstname'] . ' ' . $rs['surname'];
		}
		return $Data;
	}

	public function getUserById ($id)
	{
		$sql = 'SELECT
					UD.userid as id,
					UD.firstname,
					UD.surname,
					UD.email,
					UD.description,
					UD.lastlogged,
					G.name as groupname,
					G.idgroup,
					U.active,
					U.globaluser
					FROM userdata UD
					LEFT JOIN usergroup UG ON UD.userid = UG.userid
					LEFT JOIN user U ON U.iduser = UD.userid
					LEFT JOIN `group` G ON G.idgroup = UG.groupid
					WHERE UD.userid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'firstname' => $rs['firstname'],
				'surname' => $rs['surname'],
				'email' => $rs['email'],
				'description' => $rs['description'],
				'lastlogged' => $rs['lastlogged'],
				'groupname' => $rs['groupname'],
				'idgroup' => $rs['idgroup'],
				'active' => $rs['active'],
				'globaluser' => $rs['globaluser'],
				'photo' => $this->getPhotoUserById($id),
				'layer' => $this->getLayersById($id)
			);
			return $Data;
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function getLayersById ($id)
	{
		
		$sql = 'SELECT
					UGV.viewid,
					V.storeid,
					UGV.groupid
					FROM usergroupview UGV
					lEFT JOIN view V ON UGV.viewid = V.idview
					WHERE UGV.userid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'store' => $rs['storeid'],
				'view' => $rs['viewid'],
				'group' => $rs['groupid']
			);
		}
		
		return $Data;
	
	}

	public function getPhotoUserById ($id)
	{
		$sql = 'SELECT
					photoid
					FROM userdata UD
					LEFT JOIN file F ON F.idfile = UD.photoid
					WHERE UD.userid=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data = $rs['photoid'];
		}
		return $Data;
	}

	public function getPhotos (&$users)
	{
		if (! is_array($users)){
			throw new Exception('Wrong array given');
		}
		foreach ($users['photo'] as $photo){
			$users['photo']['small'][] = App::getModel('gallery')->getSmallImageById($photo['photoid']);
		}
	}

	public function getUserHistorylogView ($id)
	{
		$sql = 'SELECT
					UD.userid AS id,
					UHL.`URL` AS adress,
					UHL.sessionid,
					UHL.adddate
					FROM userdata UD
					LEFT JOIN userhistorylog UHL ON UHL.userid = UD.userid
					WHERE UD.userid=:id';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'sessionid' => $rs['sessionid'],
				'adress' => $rs['adress'],
				'adddate' => $rs['adddate']
			);
		}
		return $Data;
	}

	protected function updateUserLogin ()
	{
		try{		
			$sql = 'SELECT email FROM userdata WHERE userid = :iduser';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('iduser', $this->registry->core->getParam());
			$stmt->execute();
			$rs = $stmt->fetch();

			$hash = new \PasswordHash\PasswordHash();
			$sql = 'UPDATE user SET	login = :login WHERE iduser = :iduser';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('iduser', $this->registry->core->getParam());
			$stmt->bindValue('login', $hash->HashLogin($rs['email']));		

			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_LOGINUSER_UPDATE'), 17, $e->getMessage());
		}
	}

	public function updateUserPassword ($password)
	{
		if (isset($password) && ! empty($password)){
			$hash = new \PasswordHash\PasswordHash();			
			$sql = 'UPDATE user SET
						password = :password
					WHERE iduser = :iduser';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('password', $hash->HashPassword($password));
			$stmt->bindValue('iduser', $this->registry->core->getParam());
			
			try{
				$stmt->execute();
				return true;
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PASSWORDUSER_UPDATE'), 18, $e->getMessage());
			}
		}
	}

	public function updateUser ($Data, $userId)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->updateUserData($Data, $userId);
			$this->updateUserGroup($Data);
			$this->updateUserActive($Data['additional_data']['active']);
			$this->updateUserLogin();
		
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_USER_UPDATE'), 118, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		if ($userId == App::getContainer()->get('session')->getActiveUserid()){
			// App::getContainer()->get('session')->flush();
			// App::redirect('login');
		}
	}

	public function checkActiveUserIsGlobal ()
	{
		$sql = 'SELECT
					globaluser
					FROM user WHERE iduser = :userid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('userid', App::getContainer()->get('session')->getActiveUserid());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			return $rs['globaluser'];
		}
	}

	protected function updateUserGroup ($Data)
	{
		
		$global = (int) $Data['rights_data']['global'];
		
		$sql = 'UPDATE user SET
					globaluser = :globaluser
				WHERE iduser = :userid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('userid', $this->registry->core->getParam());
		$stmt->bindValue('globaluser', $global);
		
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_USER_TO_GROUP'), 21, $e->getMessage());
		}
		
		DbTracker::deleteRows('usergroup', 'userid', (int)$this->registry->core->getParam());
		
		DbTracker::deleteRows('usergroupview', 'userid', (int)$this->registry->core->getParam());
		
		if ($global == 1){
			
			$sql = 'INSERT INTO usergroup SET
						groupid = :groupid,
						userid = :userid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('userid', $this->registry->core->getParam());
			$stmt->bindValue('groupid', $Data['rights_data']['group']);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_USER_TO_GROUP'), 21, $e->getMessage());
			}
		
		}
		else{
			
			$layers = $this->getLayersAll();
			
			foreach ($layers as $key => $store){
				if (is_array($Data['rights_data']['store_' . $store['id']]) && ! empty($Data['rights_data']['store_' . $store['id']])){
					foreach ($store['views'] as $v => $view){
						
						$groupid = $Data['rights_data']['store_' . $store['id']]['view_' . $view['id']];
						
						if ($groupid > 0){
							
							$sql = 'INSERT INTO usergroupview SET
		           						userid = :userid,
		           						groupid = :groupid,
		           						viewid = :viewid
										';
							$stmt = Db::getInstance()->prepare($sql);
							$stmt->bindValue('userid', $this->registry->core->getParam());
							$stmt->bindValue('groupid', $groupid);
							$stmt->bindValue('viewid', $view['id']);
							$stmt->execute();
						
						}
					
					}
				
				}
			
			}
		
		}
	
	}

	protected function updateUserActive ($active)
	{
		$sql = 'UPDATE user SET
					active=:active
				WHERE iduser = :iduser';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('active', $active);
		$stmt->bindValue('iduser', $this->registry->core->getParam());
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_USER_ACTIVE_UPDATE'), 19, $e->getMessage());
		}
	}

	protected function updateUserData ($Data, $userid)
	{
		$sql = 'UPDATE userdata SET
					firstname = :firstname,
					surname = :surname,
					email = :email,
					description = :description,
					photoid =  :photo
					WHERE userid = :userid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('firstname', $Data['personal_data']['firstname']);
		$stmt->bindValue('surname', $Data['personal_data']['surname']);
		$stmt->bindValue('email', $Data['personal_data']['email']);
		$stmt->bindValue('description', $Data['additional_data']['description']);
		$stmt->bindValue('userid', $userid);
		if (($Data['photos_pane']['photo'][0]) > 0){
			$stmt->bindValue('photo', $Data['photos_pane']['photo'][0]);
		}
		else{
			$stmt->bindValue('photo', NULL);
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_USERDATA_UPDATE'), 19, $e->getMessage());
		}
	}

	protected function addUser ($email, $password, $active = 1)
	{
		if ($email == ''){
			throw new CoreException($this->trans('TXT_WRONG_EMAIL'), 1001, 'Email is blank -> mysql fix');
		}
		if ($password == NULL){
			$password = 'topsecret';
		}
		$hash = new \PasswordHash\PasswordHash();		
		$sql = 'INSERT INTO user SET
					login = :login,
					password = :password,
					active = :active';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('login', $hash->HashLogin($email));
		$stmt->bindValue('password', $hash->HashPassword($password));
		$stmt->bindValue('active', $active);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_USER_ADD'), 20, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	protected function addUserData ($Data, $userId)
	{
		$sql = 'INSERT INTO userdata SET
					firstname = :firstname,
					surname = :surname,
					email = :email,
					description = :description,
					userid = :userid,
					photoid = :photoid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('firstname', $Data['personal_data']['firstname']);
		$stmt->bindValue('surname', $Data['personal_data']['surname']);
		$stmt->bindValue('email', $Data['personal_data']['email']);
		$stmt->bindValue('description', $Data['additional_data']['description']);
		$stmt->bindValue('userid', $userId);
		if (($Data['photos_pane']['photo'][0]) > 0){
			$stmt->bindValue('photoid', $Data['photos_pane']['photo'][0]);
		}
		else{
			$stmt->bindValue('photoid', NULL);
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_USER_ADD'), 20, $e->getMessage());
		}
		return true;
	}

	protected function addUserToGroup ($Data, $userId)
	{
		$global = (int) $Data['rights_data']['global'];
		
		$sql = 'UPDATE user SET
					globaluser = :globaluser
				WHERE iduser = :userid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('userid', $userId);
		$stmt->bindValue('globaluser', $global);
		
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_USER_TO_GROUP'), 21, $e->getMessage());
		}
		
		DbTracker::deleteRows('usergroupview', 'userid', $userId);
		
		DbTracker::deleteRows('usergroup', 'userid', $userId);
		
		if ($global == 1){
			
			$sql = 'INSERT INTO usergroup SET
						groupid = :groupid,
						userid = :userid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('userid', $userId);
			$stmt->bindValue('groupid', $Data['rights_data']['group']);
			
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_USER_TO_GROUP'), 21, $e->getMessage());
			}
		
		}
		else{
			
			$layers = $this->getLayersAll();
			
			foreach ($layers as $key => $store){
				if (is_array($Data['rights_data']['store_' . $store['id']]) && ! empty($Data['rights_data']['store_' . $store['id']])){
					foreach ($store['views'] as $v => $view){
						
						$groupid = $Data['rights_data']['store_' . $store['id']]['view_' . $view['id']];
						
						if ($groupid > 0){
							
							$sql = 'INSERT INTO usergroupview SET
		           						userid = :userid,
		           						groupid = :groupid,
		           						viewid = :viewid
										';
							$stmt = Db::getInstance()->prepare($sql);
							$stmt->bindValue('userid', $userId);
							$stmt->bindValue('groupid', $groupid);
							$stmt->bindValue('viewid', $view['id']);
							
							$stmt->execute();
						
						}
					
					}
				
				}
			
			}
		
		}
	
	}

	public function addNewUser ($Data, $password)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newUserId = $this->addUser($Data['personal_data']['email'], $password);
			$this->addUserData($Data, $newUserId);
			$this->addUserToGroup($Data, $newUserId);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_USER_ADD'), 21, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function getUserFullName ()
	{
		if (App::getContainer()->get('session')->getActiveUserid() !== NULL){
			$fullName = App::getContainer()->get('session')->getActiveUserFirstname() . ' ' . App::getContainer()->get('session')->getActiveUserSurname();
			return $fullName;
		}
	}

	public function getActiveUserid ()
	{
		if (App::getContainer()->get('session')->getActiveUserid() !== NULL){
			return App::getContainer()->get('session')->getActiveUserid();
		}
	}

	public function getLastLoggedUsers ()
	{
		$sql = 'SELECT
					userid,
					firstname,
					surname,
					lastlogged,
					photoid
					FROM userdata
					WHERE lastlogged > 0
					ORDER BY lastlogged DESC
					LIMIT 5';
		$stmt = Db::getInstance()->prepare($sql);
		return $stmt->fetchAll();
	}

	public function setLoginTime ()
	{
		$sql = 'UPDATE userdata SET lastlogged = NOW() WHERE userid = :userid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('userid', App::getContainer()->get('session')->getActiveUserid());
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_LOGINTIME'), 22, $e->getMessage());
		}
	}

	public function getUserData ()
	{
		if (App::getContainer()->get('session')->getActiveUserid() == 0){
			return false;
		}
		$sql = 'SELECT
					UD.firstname,
					UD.surname,
					UD.email,
					UG.groupid,
					U.globaluser
					FROM userdata UD
					LEFT JOIN user U ON UD.userid = U.iduser
					LEFT JOIN usergroup UG ON UG.userid = UD.userid
					WHERE UD.userid = :userid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('userid', App::getContainer()->get('session')->getActiveUserid());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			App::getContainer()->get('session')->setActiveUserFirstname($rs['firstname']);
			App::getContainer()->get('session')->setActiveUserSurname($rs['surname']);
			App::getContainer()->get('session')->setActiveUserEmail($rs['email']);
			App::getContainer()->get('session')->setActiveUserGroupid($rs['groupid']);
			App::getContainer()->get('session')->setActiveStoreData($this->getUserStoresDataByGroupId($rs['groupid']));
			App::getContainer()->get('session')->setActiveUserIsGlobal($rs['globaluser']);
		}
		return true;
	}

	public function getUserStoresDataByGroupId ($groupid)
	{
		$globalUser = App::getContainer()->get('session')->getActiveUserIsGlobal();
		if ($globalUser == 0){
			$sql = 'SELECT DISTINCT storeid
						FROM `view` V
						LEFT JOIN usergroupview UGV ON UGV.viewid = V.idview
						WHERE UGV.userid = :userid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('userid', App::getContainer()->get('session')->getActiveUserid());
			$stmt->execute();
			$Data = Array();
			while ($rs = $stmt->fetch()){
				if ($rs['storeid'] == NULL){
					$Data['global'] = 1;
				}
				else{
					$Data['stores'] = Array(
						$rs['storeid'] => $this->getUserViewDataByStoreId($rs['storeid'])
					);
				}
			}
		}
		else{
		
		}
		return $Data;
	}

	public function getUserViewDataByStoreId ($storeid)
	{
		$sql = 'SELECT DISTINCT idview FROM `view` WHERE storeid = :storeid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('storeid', $storeid);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['idview'];
		}
		return $Data;
	}

	public function getLayerIdByViewId ($id)
	{
		$globaluser = App::getContainer()->get('session')->getActiveUserIsGlobal();
		
		$Stores = App::getContainer()->get('session')->getActiveStoreData();
		
		if ($globaluser == 1){
			return 0;
		}
		
		if ($id == 0){
			if (isset($Stores['global'])){
				return 0;
			}
			else{
				// throw new Exception('No privileges '.$id);
			}
		}
		
		foreach ($Stores as $key => $store){
			if ($key != 'global'){
				foreach ($store as $storeKey => $view){
					if ($id == $view){
						return $key;
					}
				}
			}
		}
		
		// throw new Exception('No privileges '.$id);
	}

	public function getLayersAll ()
	{
		$sql = "SELECT S.idstore AS id, S.shortcompanyname AS name,COUNT(V.idview) as views
					FROM store S
					LEFT JOIN view V ON V.storeid = S.idstore
					GROUP BY S.idstore
					HAVING views > 0";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		
		while ($rs = $stmt->fetch()){
			
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $rs['name'],
				'views' => App::getModel('view')->getViewsByStoreId($rs['id'])
			);
		}
		
		return $Data;
	
	}
}
