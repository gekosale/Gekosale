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
 * $Id: client.php 583 2011-10-28 20:19:07Z gekosale $
 */
namespace Gekosale;

class ClientModel extends Component\Model
{

	public function countriesList ()
	{
		$sql = 'SELECT C.idcountry as countryid, C.name
				FROM country C';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['countryid']] = $rs['name'];
		}
		return $Data;
	}

	public function getClient ()
	{
		if (App::getContainer()->get('session')->getActiveClientid() != NULL){
			$sql = "SELECT 	 
						AES_DECRYPT(CD.surname, :encryptionkey) AS surname,
						AES_DECRYPT(CD.firstname, :encryptionkey) AS firstname,
						AES_DECRYPT(CD.phone, :encryptionkey) AS phone,
						AES_DECRYPT(CD.phone2, :encryptionkey) AS phone2,
						CA.idclientaddress,
						AES_DECRYPT(CA.street, :encryptionkey) AS street,
						AES_DECRYPT(CA.streetno, :encryptionkey) AS streetno,
						AES_DECRYPT(CA.postcode, :encryptionkey) AS postcode,
						AES_DECRYPT(CA.placename, :encryptionkey) AS placename,
						AES_DECRYPT(CA.placeno, :encryptionkey) AS placeno,
						AES_DECRYPT(CA.nip, :encryptionkey) AS nip,
						AES_DECRYPT(CA.companyname, :encryptionkey) AS companyname,
						CA.countryid,
						AES_DECRYPT(CD.email, :encryptionkey) AS email
					FROM clientdata CD
					LEFT JOIN client C ON C.idclient=CD.clientid
					LEFT JOIN clientaddress CA ON CA.clientid=CD.clientid
					WHERE C.idclient= :clientid AND C.viewid= :viewid";
			$Data = Array();
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
			$stmt->bindValue('viewid', Helper::getViewId());
			$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
			$stmt->execute();
			$rs = $stmt->fetch();
			try{
				if ($rs){
					$Data = Array(
						'firstname' => $rs['firstname'],
						'surname' => $rs['surname'],
						'idclientaddress' => $rs['idclientaddress'],
						'phone' => $rs['phone'],
						'phone2' => $rs['phone2'],
						'street' => $rs['street'],
						'streetno' => $rs['streetno'],
						'postcode' => $rs['postcode'],
						'placename' => $rs['placename'],
						'placeno' => $rs['placeno'],
						'nip' => $rs['nip'],
						'companyname' => $rs['companyname'],
						'email' => $rs['email'],
						'countryid' => $rs['countryid']
					);
					return $Data;
				}
			}
			catch (Exception $e){
				throw new FrontendException($this->trans('ERR_CLIENT_NO_EXIST'));
			}
		}
	}

	public function getClientAddress ($main)
	{
		$sql = "SELECT 	 
					idclientaddress,
					AES_DECRYPT(firstname, :encryptionkey) AS firstname,
					AES_DECRYPT(surname, :encryptionkey) AS surname,
					AES_DECRYPT(companyname, :encryptionkey) AS companyname,
					AES_DECRYPT(nip, :encryptionkey) AS nip,
					AES_DECRYPT(street, :encryptionkey) AS street,
					AES_DECRYPT(streetno, :encryptionkey) AS streetno,
					AES_DECRYPT(postcode, :encryptionkey) AS postcode,
					AES_DECRYPT(placename, :encryptionkey) AS placename,
					AES_DECRYPT(placeno, :encryptionkey) AS placeno,
					countryid,
					clienttype
				FROM clientaddress
				WHERE clientid=:clientid AND main = :main";
		$Data = Array(
			'idclientaddress' => 0,
			'firstname' => '',
			'surname' => '',
			'companyname' => '',
			'nip' => '',
			'street' => '',
			'streetno' => '',
			'placeno' => '',
			'placename' => '',
			'postcode' => '',
			'clienttype' => 1,
			'countryid' => $this->registry->loader->getParam('countryid')
		);
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->bindValue('main', $main);
		$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		$rs = $stmt->fetch();
		try{
			if ($rs){
				$Data = Array(
					'idclientaddress' => $rs['idclientaddress'],
					'firstname' => $rs['firstname'],
					'surname' => $rs['surname'],
					'companyname' => $rs['companyname'],
					'nip' => $rs['nip'],
					'street' => $rs['street'],
					'streetno' => $rs['streetno'],
					'placeno' => $rs['placeno'],
					'placename' => $rs['placename'],
					'postcode' => $rs['postcode'],
					'countryid' => $rs['countryid'],
					'clienttype' => $rs['clienttype']
				);
			}
		}
		catch (Exception $e){
			throw new FrontendException($this->trans('ERR_CLIENT_NO_EXIST'));
		}
		return $Data;
	}

	public function updateClientAddress ($Data, $main)
	{
		$sql = 'INSERT INTO clientaddress SET 
					clientid	= :clientid,
					main		= :main,
					firstname 	= AES_ENCRYPT(:firstname, :encryptionKey), 
					surname   	= AES_ENCRYPT(:surname, :encryptionKey), 
					companyname	= AES_ENCRYPT(:companyname, :encryptionKey), 
					street		= AES_ENCRYPT(:street, :encryptionKey), 
					streetno	= AES_ENCRYPT(:streetno, :encryptionKey),
					placeno		= AES_ENCRYPT(:placeno, :encryptionKey),
					postcode	= AES_ENCRYPT(:postcode, :encryptionKey),
					nip			= AES_ENCRYPT(:nip, :encryptionKey),
					placename	= AES_ENCRYPT(:placename, :encryptionKey),
					countryid	= :countryid,
					clienttype	= :clienttype
				ON DUPLICATE KEY UPDATE 
					firstname 	= AES_ENCRYPT(:firstname, :encryptionKey), 
					surname   	= AES_ENCRYPT(:surname, :encryptionKey), 
					companyname	= AES_ENCRYPT(:companyname, :encryptionKey), 
					street		= AES_ENCRYPT(:street, :encryptionKey), 
					streetno	= AES_ENCRYPT(:streetno, :encryptionKey),
					placeno		= AES_ENCRYPT(:placeno, :encryptionKey),
					postcode	= AES_ENCRYPT(:postcode, :encryptionKey),
					nip			= AES_ENCRYPT(:nip, :encryptionKey),
					placename	= AES_ENCRYPT(:placename, :encryptionKey),
					countryid	= :countryid,
					clienttype	= :clienttype';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		$stmt->bindValue('main', $main);
		$stmt->bindValue('firstname', $Data['firstname']);
		$stmt->bindValue('surname', $Data['surname']);
		$stmt->bindValue('companyname', $Data['companyname']);
		$stmt->bindValue('street', $Data['street']);
		$stmt->bindValue('streetno', $Data['streetno']);
		$stmt->bindValue('postcode', $Data['postcode']);
		$stmt->bindValue('placeno', $Data['placeno']);
		$stmt->bindValue('nip', $Data['nip']);
		$stmt->bindValue('placename', $Data['placename']);
		$stmt->bindValue('countryid', isset($Data['country']) ? $Data['country'] : $Data['countryid']);
		$stmt->bindValue('clienttype', isset($Data['clienttype']) ? $Data['clienttype'] : 1);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		return true;
	}

	public function getClientPass ()
	{
		$sql = "SELECT password 
					FROM client
					WHERE idclient= :idclient 
						AND viewid= :viewid";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idclient', App::getContainer()->get('session')->getActiveClientid());
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data = Array(
				'password' => $rs['password']
			);
		}
		else{
			throw new FrontendException($this->trans('ERR_PASSWORD_NOT_EXIST'));
		}
		return $Data;
	}

	public function addNewClient ($Data, $password = null)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newClientId = $this->addClient($Data);
			$this->addClientData($Data, $newClientId);
		}
		catch (Exception $e){
			throw new FrontendException($this->trans('ERR_NEWCLIENT_ADD'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return $newClientId;
	}

	public function checkClientLink ($link)
	{
		$sql = 'SELECT
					login,
					password,
					idclient
				FROM client
				WHERE 
					activelink = :activelink
				';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('activelink', $link);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'email' => $rs['login'],
				'password' => $rs['password']
			);
			$this->updateClientDisable($rs['idclient'], 0);
		}
		return $Data;
	}

	public function updateClientDisable ($id, $disable, $activelink = NULL)
	{
		$sql = 'UPDATE client SET
					disable = :disable,
					activelink = :activelink
				WHERE idclient = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		if ($disable == 1){
			$stmt->bindValue('disable', $disable);
			$stmt->bindValue('activelink', $activelink);
		}
		else{
			$stmt->bindValue('disable', 0);
			$stmt->bindValue('activelink', NULL);
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($this->trans('ERR_CLIENT_ADD'), 4, $e->getMessage());
		}
		return $activelink;
	}

	protected function addClient ($Data, $disable = 0)
	{
		$sql = 'INSERT INTO client SET
					login = :login, 
					password = :password, 
					disable = :disable, 
					viewid = :viewid,
					facebookid = :facebookid,
					activelink = :activelink';
		$stmt = Db::getInstance()->prepare($sql);
		
		$hash = new \PasswordHash\PasswordHash();
		$stmt->bindValue('login', $hash->HashLogin($Data['email']));
		$stmt->bindValue('password', $hash->HashPassword($Data['password']));
		$stmt->bindValue('disable', $disable);
		$stmt->bindValue('activelink', NULL);
		$stmt->bindValue('viewid', Helper::getViewId());
		if (isset($Data['facebookid']) && $Data['facebookid'] != ''){
			$stmt->bindValue('facebookid', $Data['facebookid']);
		}
		else{
			$stmt->bindValue('facebookid', NULL);
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($this->trans('ERR_CLIENT_ADD'), 4, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function getDefaultClientGroupId ()
	{
		$sql = 'SELECT clientgroupid 
					FROM assigntogroup 
					WHERE viewid = :viewid 
					ORDER BY `from` ASC LIMIT 1';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			return $rs['clientgroupid'];
		}
	}

	protected function addClientData ($Data, $ClientId)
	{
		$sql = 'INSERT INTO clientdata(
						firstname, 
						surname, 
						email, 
						phone,
						clientid,
						clientgroupid
					)
					VALUES (
						AES_ENCRYPT(:firstname, :encryptionKey), 
						AES_ENCRYPT(:surname, :encryptionKey),
						AES_ENCRYPT(:email, :encryptionKey),  
						AES_ENCRYPT(:phone, :encryptionKey),
						:clientid,
						:clientgroupid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('firstname', $Data['firstname']);
		$stmt->bindValue('surname', $Data['surname']);
		$stmt->bindValue('email', $Data['email']);
		$stmt->bindValue('phone', $Data['phone']);
		$stmt->bindValue('clientid', $ClientId);
		$groupid = $this->getDefaultClientGroupId();
		if ($groupid > 0){
			$stmt->bindValue('clientgroupid', $groupid);
		}
		else{
			$stmt->bindValue('clientgroupid', NULL);
		}
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		try{
			$stmt->execute();
			
			if (isset($Data['newsletter']) && $Data['newsletter'] == 1){
				$sendingo = App::getmodel('sendingo');
				$viewId = Helper::getViewId();

				if ($sendingo->emailExists($Data['email'], $viewId)) {
					$sendingo->activeEmail($Data['email'], $viewId);
				}
				else {
					$newId = App::getModel('newsletter')->addClientAboutNewsletter($Data['email']);
					if ($newId > 0){
						App::getModel('newsletter')->changeNewsletterStatus($newId);
						$sendingoId = $sendingo->sendingoAddEmail($Data['email'], $viewId);
						$sendingo->updateSendingoId($Data['email'], $viewId, $sendingoId);
					}
				}
			}
		}
		catch (Exception $e){
			throw new FrontendException($this->trans('ERR_CLIENTDATA_ADD'), 4, $e->getMessage());
		}
		return true;
	}

	public function updateClientPass ($password)
	{
		if (isset($password) && ! empty($password)){
			$hash = new \PasswordHash\PasswordHash();
			$sql = 'UPDATE client SET password = :password WHERE idclient = :idclient';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('password', $hash->HashPassword($password));
			$stmt->bindValue('idclient', App::getContainer()->get('session')->getActiveClientid());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new FrontendException($this->trans('ERR_PASSWORD_CLIENT_UPDATE'), 18, $e->getMessage());
			}
		}
	}

	public function updateClientLogin ($login)
	{
		if (isset($login) && ! empty($login)){
			$hash = new \PasswordHash\PasswordHash();
			$sql = 'UPDATE client SET login = :login WHERE idclient = :idclient';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('login', $hash->HashLogin($login));
			$stmt->bindValue('idclient', App::getContainer()->get('session')->getActiveClientid());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new FrontendException($this->trans('ERR_LOGIN_CLIENT_UPDATE'), 18, $e->getMessage());
			}
		}
	}

	public function updateClientEmail ($Data)
	{
		$sql = 'UPDATE clientdata SET 
					email = AES_ENCRYPT(:email, :encryptionKey)
				WHERE clientid=:clientid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		$stmt->bindValue('email', $Data['email']);
		try{
			$stmt->execute();
			App::getContainer()->get('session')->setActiveClientEmail($Data['email']);
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		return true;
	}

	public function updateClientPhone ($phone, $phone2)
	{
		$sql = 'UPDATE clientdata SET 
					phone = AES_ENCRYPT(:phone, :encryptionKey),
					phone2 = AES_ENCRYPT(:phone2, :encryptionKey)
				WHERE clientid = :clientid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		$stmt->bindValue('phone', $phone);
		$stmt->bindValue('phone2', $phone2);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		return true;
	}

	public function checkClientNewMail ($Data)
	{
		$sql = "SELECT 
					idclientdata 
				FROM clientdata 
				LEFT JOIN client C ON C.idclient = clientid
				WHERE AES_DECRYPT(email, :encryptionKey) = :newmail AND C.viewid = :viewid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('newmail', $Data['email']);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$ismail = 0;
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$result = $rs['idclientdata'];
			if ($result > 0)
				$ismail = 1;
		}
		return $ismail;
	}

	public function updateClient ($Data)
	{
		$sql = 'UPDATE clientaddress 
					SET 
						firstname=AES_ENCRYPT(:firstname, :encryptionKey), 
						surname=AES_ENCRYPT(:surname, :encryptionKey), 
						companyname=AES_ENCRYPT(:companyname, :encryptionKey), 
						street=AES_ENCRYPT(:street, :encryptionKey), 
						streetno=AES_ENCRYPT(:streetno, :encryptionKey),
						placeno=AES_ENCRYPT(:placeno, :encryptionKey),
						postcode=AES_ENCRYPT(:postcode, :encryptionKey),
						nip=AES_ENCRYPT(:nip, :encryptionKey),
						placename=AES_ENCRYPT(:placename, :encryptionKey)
					WHERE clientid= :clientid AND idclientaddress= :idclientaddress';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		$stmt->bindValue('idclientaddress', $Data['idclientaddress']);
		$stmt->bindValue('firstname', $Data['firstname']);
		$stmt->bindValue('surname', $Data['surname']);
		$stmt->bindValue('companyname', $Data['companyname']);
		$stmt->bindValue('street', $Data['street']);
		$stmt->bindValue('streetno', $Data['streetno']);
		$stmt->bindValue('postcode', $Data['postcode']);
		$stmt->bindValue('placeno', $Data['placeno']);
		$stmt->bindValue('nip', $Data['nip']);
		$stmt->bindValue('placename', $Data['placename']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		return true;
	}

	public function saveClientData ()
	{
		if (App::getContainer()->get('session')->getActiveClientid() == 0){
			return false;
		}
		$sql = 'SELECT 
					AES_DECRYPT(email, :encryptionkey) AS email, 
					AES_DECRYPT(firstname, :encryptionkey) AS firstname,  
					AES_DECRYPT(surname, :encryptionkey) AS surname,
					clientgroupid
				FROM clientdata
				LEFT JOIN client C ON C.idclient= :clientid
				WHERE clientid= :clientid AND C.viewid= :viewid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			App::getContainer()->get('session')->setActiveClientFirstname($rs['firstname']);
			App::getContainer()->get('session')->setActiveClientSurname($rs['surname']);
			App::getContainer()->get('session')->setActiveClientEmail($rs['email']);
			App::getContainer()->get('session')->setActiveClientGroupid($rs['clientgroupid']);
		}
		return true;
	}
}
