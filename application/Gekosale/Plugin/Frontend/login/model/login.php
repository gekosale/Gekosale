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
 * $Id: login.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Plugin;

class LoginModel extends Component\Model
{

	public function authProccess ($login, $password)
	{
		$hash = new \PasswordHash\PasswordHash();
		$sql = 'SELECT DISTINCT iduser, password FROM user U
				WHERE login = :login AND active = 1';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('login', $hash->HashLogin($login));
		$stmt->execute();
		$rs = $stmt->fetch();
		$id = 0;
		if ($rs){
			if ($hash->CheckPassword($password, $rs['password'])){
				$id = $rs['iduser'];
			}
		}
		return $id;
	}

	public function authProccessSha ($sha)
	{
		$sql = 'SELECT DISTINCT iduser FROM user U
				WHERE SHA1(CONCAT(login,password)) = :hash AND active = 1';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('hash', $sha);
		$stmt->execute();
		$rs = $stmt->fetch();
		$id = 0;
		if ($rs){
			$id = $rs['iduser'];
		}
		return $id;
	}

	public function checkInstanceIsValid ()
	{
		$this->instance = new Instance();
		$this->instance->setEnviromentVariables();
	}

	public function checkUsers ($login)
	{
		$hash = new \PasswordHash\PasswordHash();
		$sql = 'SELECT iduser FROM user U
				WHERE login = :login AND active = 1';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('login', $hash->HashLogin($login));
		$stmt->execute();
		$rs = $stmt->fetch();
		$id = 0;
		if ($rs){
			$id = $rs['iduser'];
		}
		return $id;
	}

	public function getUserStoresDataByGroupId ($groupid)
	{
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
				$Data[$rs['storeid']] = $this->getUserViewDataByStoreId($rs['storeid']);
			}
		}
		return $Data;
	}

	public function getUserViewDataByStoreId ($storeid)
	{
		$sql = 'SELECT idview FROM `view` WHERE storeid = :storeid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('storeid', $storeid);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['idview'];
		}
		return $Data;
	}

	public function changeUsersPassword ($id, $password)
	{
		$hash = new \PasswordHash\PasswordHash();
		$sql = 'UPDATE user SET password=:password
					WHERE iduser=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('password', $hash->HashPassword($password));
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PASSWORD_USER_FORGOT'), 13, $e->getMessage());
			return false;
		}
		return true;
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

	public function setDefaultView ($result)
	{
		$sql = 'SELECT globaluser FROM user WHERE iduser = :userid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('userid', $result);
		$stmt->execute();
		$rs = $stmt->fetch();
		
		if ($rs){
			$globaluser = $rs['globaluser'];
		}
		
		if ($globaluser == 0){
			$sql = 'SELECT
						UGV.viewid,
						V.storeid,
						V.name as viewname,
						UGV.groupid,
						S.idstore as storeid,
						S.shortcompanyname as storename
						FROM usergroupview UGV 
						lEFT JOIN view V ON UGV.viewid = V.idview
						lEFT JOIN store S ON V.storeid = S.idstore
						WHERE UGV.userid = :userid
						LIMIT 1';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('userid', App::getContainer()->get('session')->getActiveUserid());
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				App::getContainer()->get('session')->setActiveStoreId($rs['storeid']);
				App::getContainer()->get('session')->setActiveViewId($rs['viewid']);
				return true;
			}
		}
		else{
			$sql = 'SELECT
						idview,
						storeid
					FROM view
					LIMIT 1';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				App::getContainer()->get('session')->setActiveStoreId($rs['storeid']);
				App::getContainer()->get('session')->setActiveViewId($rs['idview']);
			}
		}
	}

	public function destroyAdminAutologinKey ()
	{
		if (isset($_COOKIE['autologin_admin'])){
			setcookie('autologin_admin', '', time() - 3600, '/', null, false, true);
		}
	}

	public function setAdminAutologinKey ($id)
	{
		$sql = "SELECT SHA1(CONCAT(login, password)) as hash FROM user WHERE iduser = :iduser AND active = 1";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('iduser', $id);
		$stmt->execute();
		
		setcookie('autologin_admin', $stmt->fetchColumn(), time() + 24 * 60 * 60 * 30, '/', null, false, true);
	}

	public function getAdminAutologinKey ()
	{
		if (! isset($_COOKIE['autologin_admin'])){
			return false;
		}
		
		$result = $this->authProccessSha((string) $_COOKIE['autologin_admin']);
		
		if (! $result){
			// delete cookie
			setcookie('autologin_admin', '', time() - 3600, '/', null, false, true);
			return false;
		}
		
		App::getContainer()->get('session')->setActiveLoginError(null);
		App::getContainer()->get('session')->setActiveUserid($result);
		$this->setLoginTime();
		$this->getUserData();
		$this->setDefaultView($result);
		App::redirect(__ADMINPANE__ . '/mainside');
	}
}
?>
