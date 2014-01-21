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
 * $Revision: 583 $
 * $Author: gekosale $
 * $Date: 2011-10-28 22:19:07 +0200 (Pt, 28 paź 2011) $
 * $Id: clientlogin.php 583 2011-10-28 20:19:07Z gekosale $
 */
namespace Gekosale;

use DateTime;
use xajaxResponse;

class ClientLoginModel extends Component\Model
{

	public function authProccess ($login, $password)
	{
		$hash = new \PasswordHash\PasswordHash();
		$login = App::getModel('formprotection')->cropDangerousCode($login);
		$password = App::getModel('formprotection')->cropDangerousCode($password);
		$sql = 'SELECT idclient, disable, password FROM client WHERE login = :login AND viewid=:viewid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('login', $hash->HashLogin($login));
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			if ($rs['disable'] == 0){
				if ($hash->CheckPassword($password, $rs['password'])){
					return $rs['idclient'];
				}
				else{
					return 0;
				}
			}
			else{
				return - 1;
			}
		}
		else{
			return 0;
		}
	}

	public function authProccessConfirmation ($login, $password)
	{
		$login = App::getModel('formprotection')->cropDangerousCode($login);
		$password = App::getModel('formprotection')->cropDangerousCode($password);
		$sql = 'SELECT idclient FROM client WHERE login = :login AND password = :password AND viewid=:viewid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('login', $login);
		$stmt->bindValue('password', $password);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			return $rs['idclient'];
		}
		else{
			return 0;
		}
	}

	public function authProccessQuick ($login, $password, $autologin)
	{
		$objResponse = new xajaxResponse();
		$login = App::getModel('formprotection')->cropDangerousCode($login);
		$password = App::getModel('formprotection')->cropDangerousCode($password);
		$hash = new \PasswordHash\PasswordHash();
		$sql = 'SELECT idclient,disable,password FROM client WHERE login = :login AND viewid=:viewid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('login', $hash->HashLogin($login));
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			if ($rs['disable'] == 0){
				if ($hash->CheckPassword($password, $rs['password'])){
					$result = $rs['idclient'];
				}
				else{
					$result = 0;
				}
			}
			else{
				$result = - 1;
			}
		}
		else{
			$result = 0;
		}
		
		if ($result > 0){
			if (isset($result)){
				App::getModel('clientlogin')->setAutologinKey($result);
			}
			App::getContainer()->get('session')->setActiveClientid($result);
			$this->checkClientGroup();
			$this->setLoginTime();
			App::getModel('client')->saveClientData();
			$misingCart = App::getModel('missingcart')->checkMissingCartForClient(App::getContainer()->get('session')->getActiveClientid());
			if (is_array($misingCart) && $misingCart != 0){
				App::getModel('cart')->addProductsToCartFromMissingCart($misingCart);
				App::getModel('missingcart')->cleanMissingCart(App::getContainer()->get('session')->getActiveClientid());
			}
			$objResponse->script("window.location.reload(false);");
		}
		elseif ($result < 0){
			$message = $this->trans('TXT_BLOKED_USER');
			$objResponse->assign("login-error", "innerHTML", $message);
			$objResponse->script("$('#login-error').show();");
		}
		else{
			$message = $this->trans('ERR_BAD_LOGIN_OR_PASSWORD');
			$objResponse->assign("login-error", "innerHTML", $message);
			$objResponse->script("$('#login-error').show();");
		}
		return $objResponse;
	}

	public function setLoginTime ()
	{
		$sql = 'UPDATE clientdata SET lastlogged = NOW() WHERE clientid = :clientid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_LOGINTIME'), 22, $e->getMessage());
		}
	}

	public function setTimeInterval ()
	{
		$sql = "SELECT 
					V.periodid, 
					P.timeinterval 
				FROM view V
				LEFT JOIN period P ON P.idperiod = V.periodid 
				WHERE V.idview=:viewid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		$rs = $stmt->fetch();
		$Array = Array();
		if ($rs){
			$Array = Array(
				'timeinterval' => $rs['timeinterval']
			);
		}
		$date = new DateTime();
		$date->setDate(date("Y"), date("m"), date("d"));
		$date->modify($Array['timeinterval']);
		return $date->format("Y-m-d");
	}

	public function checkClientGroup ()
	{
		$sql = "SELECT 
					CD.clientgroupid,
					C.autoassign
				FROM clientdata CD
				LEFT JOIN assigntogroup AG ON AG.clientgroupid = CD.clientgroupid
				LEFT JOIN client C ON C.idclient = CD.clientid
				WHERE clientid = :clientid AND C.viewid=:viewid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		$stmt->execute();
		$rs = $stmt->fetch();
		$group = 0;
		$Data = Array();
		if ($rs){
			$Data = Array(
				'clientgroupid' => $rs['clientgroupid'],
				'autoassign' => $rs['autoassign']
			);
		}
		if (isset($Data['clientgroupid']) && ($Data['clientgroupid'] > 0) && isset($Data['autoassign']) && ($Data['autoassign'] == 1)){
			$group = $this->checkActiveClientGroup();
		}
		if ($group !== 0){
			return $group;
		}
		else{
			return $Data['clientgroupid'];
		}
	}

	public function checkActiveClientGroup ()
	{
		$period = $this->setTimeInterval();
		$sql = 'SELECT 
					O.clientid, 
					O.idorder, 
					SUM(O.price) as price, 
					O.orderstatusid, 
					O.viewid, 
					O.`adddate`
				FROM `order` O
				LEFT JOIN orderstatusorderstatusgroups OSOSG ON OSOSG.orderstatusid = O.orderstatusid
				LEFT JOIN view V ON V.idview = O.viewid
				WHERE OSOSG.orderstatusgroupsid = V.orderstatusgroupsid
				AND O.viewid = :viewid 
				AND O.clientid=:clientid 
				AND O.adddate > :period
				GROUP BY O.clientid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		
		$stmt->bindValue('period', $period);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		$rs = $stmt->fetch();
		$price = 0;
		$group = 0;
		$Datagroup = 0;
		if ($rs){
			$price = $rs['price'];
			if ($price == NULL){
				$price = 0.00;
			}
			
			$sql2 = 'SELECT 
						ATG.`from`, 
						ATG.`to`, 
						ATG.clientgroupid, 
						CASE
						  	WHEN (`from`<>0 AND `from` <= :price AND `to`=0) THEN clientgroupid
						  	WHEN (:price BETWEEN `from` AND `to`) THEN clientgroupid
						  	WHEN (`to` = 0 AND `from` <= :price) THEN clientgroupid
						  	WHEN (`from`=0 AND `to`=0) THEN clientgroupid
						END as groupclient
						FROM assigntogroup ATG
						WHERE viewid=:viewid';
			$stmt = Db::getInstance()->prepare($sql2);
			$stmt->bindValue('price', $price);
			$stmt->bindValue('viewid', Helper::getViewId());
			
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Datagroup = $rs['groupclient'];
				if ($Datagroup > 0){
					$group = $Datagroup;
				}
			}
			if ($group > 0){
				$this->autoAssigntoGroup($group);
			}
		}
		return $group;
	}

	public function autoAssigntoGroup ($Datagroup)
	{
		$sql = "UPDATE clientdata SET 
					clientgroupid = :clientgroupid
				WHERE clientid=:clientid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		$stmt->bindValue('clientgroupid', $Datagroup);
		try{
			$stmt->execute();
		}
		catch (Exception $fe){
			throw new FrontendException($this->trans('ERR_AUTO_ASSIGN_TO_GROUP'));
		}
	}

	public function destroyAutologinKey ()
	{
		if (isset($_COOKIE['autologin'])){
			setcookie('autologin', '', time() - 3600, '/', null, false, true);
		}
	}

	public function setAutologinKey ($id)
	{
		$sql = "SELECT SHA1(CONCAT(login, password)) as hash FROM client WHERE idclient = :idclient AND disable = 0";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idclient', $id);
		$stmt->execute();
		
		setcookie('autologin', $stmt->fetchColumn(), time() + 24 * 60 * 60 * 30, '/', null, false, true);
	}

	public function getAutologinKey ()
	{
		if (! isset($_COOKIE['autologin'])){
			return false;
		}
		
		$sql = "SELECT idclient FROM client WHERE SHA1(CONCAT(login, password)) = :hash AND disable = 0";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('hash', (string) $_COOKIE['autologin']);
		$stmt->execute();
		
		$id = $stmt->fetchColumn();
		
		if (! $id){
			// delete cookie
			setcookie('autologin', '', time() - 3600, '/', null, false, true);
			return false;
		}
		
		App::getContainer()->get('session')->setActiveClientid($id);
		App::getModel('clientlogin')->checkClientGroup();
		App::getModel('clientlogin')->setLoginTime();
		App::getModel('client')->saveClientData();
		$misingCart = App::getModel('missingcart')->checkMissingCartForClient($id);
		if (is_array($misingCart) && ! empty($misingCart)){
			App::getModel('cart')->addProductsToCartFromMissingCart($misingCart);
			App::getModel('missingcart')->cleanMissingCart(App::getContainer()->get('session')->getActiveClientid());
		}
		if (($this->Cart = App::getContainer()->get('session')->getActiveCart()) != NULL){
			App::redirectUrl($this->registry->router->generate('frontend.cart', true));
		}
		else{
			App::redirectUrl($this->registry->router->generate('frontend.home', true));
		}
	}
}
