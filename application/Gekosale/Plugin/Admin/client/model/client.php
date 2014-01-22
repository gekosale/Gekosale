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
 * $Revision: 523 $
 * $Author: gekosale $
 * $Date: 2011-09-10 10:22:41 +0200 (So, 10 wrz 2011) $
 * $Id: client.php 523 2011-09-10 08:22:41Z gekosale $ 
 */
namespace Gekosale\Plugin;

class ClientModel extends Component\Model\Datagrid
{

	public function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('clientdata', Array(
			'idclient' => Array(
				'source' => 'C.idclient'
			),
			'disable' => Array(
				'source' => 'disable'
			),
			'clientorder' => Array(
				'source' => 'IF(SUM(O.globalprice) IS NULL, 0, SUM(O.globalprice))',
				'filter' => 'having'
			),
			'firstname' => Array(
				'source' => 'CONVERT(LOWER(AES_DECRYPT(CD.firstname, :encryptionkey)) USING utf8)',
				'prepareForAutosuggest' => true
			),
			'surname' => Array(
				'source' => 'CONVERT(LOWER(AES_DECRYPT(CD.surname, :encryptionkey)) USING utf8)',
				'prepareForAutosuggest' => true
			),
			'email' => Array(
				'source' => 'CD.email',
				'encrypted' => true
			),
			'groupname' => Array(
				'source' => 'CGT.name',
				'prepareForSelect' => true
			),
			'phone' => Array(
				'source' => 'CD.phone',
				'encrypted' => true
			),
			'phone2' => Array(
				'source' => 'CD.phone2',
				'encrypted' => true
			),
			'adddate' => Array(
				'source' => 'CD.adddate'
			),
			'view' => Array(
				'source' => 'V.name',
				'prepareForSelect' => true
			)
		));
		$datagrid->setFrom('
			client C
			LEFT JOIN clientdata CD ON CD.clientid = C.idclient
			LEFT JOIN clientgrouptranslation CGT ON CGT.clientgroupid = CD.clientgroupid AND CGT.languageid=:languageid
			LEFT JOIN orderclientdata OCD ON OCD.clientid = CD.clientid
			LEFT JOIN `order` O ON O.idorder = OCD.orderid
			LEFT JOIN view V ON C.viewid = V.idview
		');
		
		$datagrid->setGroupBy('C.idclient');
		
		$datagrid->setAdditionalWhere('
			C.viewid IN (' . Helper::getViewIdsAsString() . ')
		');
	}

	public function getFirstnameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('firstname', $request, $processFunction);
	}

	public function getSurnameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('surname', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getClientForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXEnableClient ($datagridId, $id)
	{
		try{
			$this->enableClient($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_ENABLE_USER')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function doAJAXDisableClient ($datagridId, $id)
	{
		try{
			$this->disableClient($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_DISABLE_USER')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function disableClient ($id)
	{
		$sql = 'UPDATE client SET disable = 1 WHERE idclient = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		
		$sql = 'DELETE FROM sessionhandler WHERE clientid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CLIENT_ACTIVE_UPDATE'), 1, $e->getMessage());
		}
	}

	public function enableClient ($id)
	{
		$sql = 'UPDATE client SET disable = 0 WHERE idclient = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function doAJAXDeleteClient ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteClient'
		), App::getRegistry()->router->getCurrentController());
	}

	public function deleteClient ($id)
	{
		DbTracker::deleteRows('client', 'idclient', $id);
	}

	public function clientGroup ($id)
	{
		$sql = 'SELECT 
					clientgroupid
				FROM clientdata
				WHERE clientid=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function clientGroupIds ($id)
	{
		$Data = $this->clientGroup($id);
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[] = $key['clientgroupid'];
		}
		return $tmp;
	}

	public function isEmailOnNewsletter ($email, $viewId)
	{
		$sql = "SELECT email FROM clientnewsletter WHERE email = :email AND viewid = :viewid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('email', $email);
		$stmt->bindValue('viewid', $viewId);
		$stmt->execute();
		
		return (boolean) $stmt->fetch();
	}

	public function getClientView ($id)
	{
		$sql = "SELECT 	
					AES_DECRYPT(CD.phone, :encryptionkey) AS phone, 
					AES_DECRYPT(CD.phone2, :encryptionkey) AS phone2,
					AES_DECRYPT(CD.description, :encryptionkey) AS description, 
					AES_DECRYPT(CD.email, :encryptionkey) AS email, 
					AES_DECRYPT(CD.firstname, :encryptionkey) AS firstname, 
					CD.clientid AS id, 
					AES_DECRYPT(CD.surname, :encryptionkey) AS surname,			
					CD.clientgroupid,
					C.disable,
					C.autoassign,
					C.viewid
				FROM clientdata CD
				LEFT JOIN clientgrouptranslation CGT ON CGT.clientgroupid = CD.clientgroupid AND CGT.languageid=:languageid
				LEFT JOIN client C ON C.idclient = CD.clientid 
				WHERE CD.clientid=:id";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('id', $id);
		$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data = Array(
				'id' => $rs['id'],
				'firstname' => $rs['firstname'],
				'surname' => $rs['surname'],
				'email' => $rs['email'],
				'phone' => $rs['phone'],
				'phone2' => $rs['phone2'],
				'description' => $rs['description'],
				'clientgroupid' => $rs['clientgroupid'],
				'disable' => $rs['disable'],
				'newsletter' => $this->isEmailOnNewsletter($rs['email'], $rs['viewid']),
				'autoassign' => $rs['autoassign'],
				'viewid' => $rs['viewid'],
				'billing_address' => $this->getClientAddress($id, 1),
				'delivery_address' => $this->getClientAddress($id, 0)
			);
			return $Data;
		}
		throw new CoreException($this->trans('ERR_CLIENT_NO_EXIST'));
	}

	public function addNewClient ($Data, $password)
	{
		Db::getInstance()->beginTransaction();
		$newClientId = NULL;
		try{
			if (Helper::getViewId() == 0){
				$viewid = $Data['personal_data']['viewid'];
			}
			else{
				$viewid = Helper::getViewId();
			}
			$newClientId = $this->addClient($Data['personal_data']['email'], $password, $viewid);
			$this->addClientData($Data, $newClientId);
			$this->updateClientAddress($Data['billing_data'], $newClientId, 1);
			$this->updateClientAddress($Data['shipping_data'], $newClientId, 0);
			$viewid = $Data['personal_data']['viewid'];
			$this->editClientActive($Data['additional_data']['disable'], $newClientId, $viewid, $Data['personal_data']['email'], $Data['personal_data']['autoassign']);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_NEWCLIENT_ADD'), 125, $e->getMessage());
		}
		Db::getInstance()->commit();
		return $newClientId;
	}

	protected function addClientAddress ($Data, $ClientId)
	{
		foreach ($Data['street'] as $key => $street){
			$sql = 'INSERT INTO clientaddress(
							street, 
							streetno, 
							placeno, 
							postcode, 
							companyname, 
							firstname, 
							surname,
							placename, 
							countryid, 
							nip, 
							clientid)
						VALUES (
							AES_ENCRYPT(:street, :encryptionKey),
							AES_ENCRYPT(:nr, :encryptionKey),
							AES_ENCRYPT(:placeno, :encryptionKey),
							AES_ENCRYPT(:postcode, :encryptionKey),
							AES_ENCRYPT(:companyname, :encryptionKey),
							AES_ENCRYPT(:firstname, :encryptionKey),
							AES_ENCRYPT(:surname, :encryptionKey),		
							AES_ENCRYPT(:placename, :encryptionKey),
							:countryid,
							AES_ENCRYPT(:nip, :encryptionKey),
							:clientid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('street', $street);
			$stmt->bindValue('nr', $Data['streetno'][$key]);
			$stmt->bindValue('placeno', $Data['placeno'][$key]);
			$stmt->bindValue('postcode', $Data['postcode'][$key]);
			$stmt->bindValue('companyname', $Data['companyname'][$key]);
			$stmt->bindValue('firstname', $Data['firstname'][$key]);
			$stmt->bindValue('surname', $Data['surname'][$key]);
			$stmt->bindValue('placename', $Data['placename'][$key]);
			$stmt->bindValue('countryid', $Data['country'][$key]);
			$stmt->bindValue('nip', $Data['nip'][$key]);
			$stmt->bindValue('clientid', $ClientId);
			$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CLIENTADRESS_ADD'), 125, $e->getMessage());
			}
		}
		return $Data;
	}

	protected function addClient ($email, $password = 'topsecret', $viewid, $active = 0)
	{
		if ($email == ''){
			throw new CoreException($this->trans('ERR_INVALID_EMAIL'));
		}
		$hash = new \PasswordHash\PasswordHash();
		$sql = 'INSERT INTO client (login, password, disable, viewid) 
				VALUES (:login, :password, :disable, :viewid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('login', $hash->HashLogin($email));
		$stmt->bindValue('password', $hash->HashPassword($password));
		$stmt->bindValue('disable', $active);
		
		if (Helper::getViewId() == 0){
			$stmt->bindValue('viewid', $viewid);
		}
		else{
			$stmt->bindValue('viewid', Helper::getViewId());
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CLIENT_ADD'), 4, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	protected function addClientData ($Data, $ClientId)
	{
		$sql = 'INSERT INTO clientdata(
					firstname, 
					surname, 
					email,  
					phone,
					phone2,
					description,
					clientgroupid,
					clientid
				)VALUES (
					AES_ENCRYPT(:firstname, :encryptionKey), 
					AES_ENCRYPT(:surname, :encryptionKey),
					AES_ENCRYPT(:email, :encryptionKey),  
					AES_ENCRYPT(:phone, :encryptionKey),  
					AES_ENCRYPT(:phone2, :encryptionKey),
					AES_ENCRYPT(:description, :encryptionKey),
					:clientgroupid,
					:clientid
				)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', $ClientId);
		$stmt->bindValue('clientgroupid', $Data['personal_data']['clientgroupid']);
		$stmt->bindValue('firstname', $Data['personal_data']['firstname']);
		$stmt->bindValue('surname', $Data['personal_data']['surname']);
		$stmt->bindValue('email', $Data['personal_data']['email']);
		$stmt->bindValue('phone', $Data['personal_data']['phone']);
		$stmt->bindValue('phone2', $Data['personal_data']['phone2']);
		$stmt->bindValue('description', $Data['additional_data']['description']);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		try{
			$stmt->execute();
			
			$sendingo = App::getModel('sendingo');
			
			if (isset($Data['personal_data']['newsletter']) && $Data['personal_data']['newsletter'] == 1){
				if ($sendingo->emailExists($Data['personal_data']['email'], $Data['personal_data']['viewid'])){
					$sendingoId = $sendingo->activeEmail($Data['email'], $Data['personal_data']['viewid']);
				}
				else{
					$newId = $this->addClientAboutNewsletter($Data['personal_data']['email'], $Data['personal_data']['viewid']);
					if ($newId > 0){
						$sendingo->activeEmail($Data['personal_data']['email'], $Data['personal_data']['viewid']);
						$sendingoId = $sendingo->sendingoAddEmail($Data['personal_data']['email'], $Data['personal_data']['viewid']);
						$sendingo->updateSendingoId($Data['personal_data']['email'], $Data['personal_data']['viewid'], $sendingoId);
					}
				}
			}
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CLIENTDATA_ADD'), 4, $e->getMessage());
		}
		return true;
	}

	public function editClient ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->editClientData($Data, $id);
			$this->updateClientAddress($Data['billing_data'], $id, 1);
			$this->updateClientAddress($Data['shipping_data'], $id, 0);
			$viewid = $Data['personal_data']['viewid'];
			$this->editClientActive($Data['additional_data']['disable'], $id, $viewid, $Data['personal_data']['email'], $Data['personal_data']['autoassign']);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CLIENT_EDIT'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function editClientActive ($active, $id, $viewid, $login, $autoassign)
	{
		$hash = new \PasswordHash\PasswordHash();
		$sql = 'UPDATE client SET 
					disable		=	:disable, 
					viewid		=	:viewid,
					login		=	:login,
					autoassign	=	:autoassign
				WHERE idclient=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('disable', (int) $active);
		$stmt->bindValue('viewid', $viewid);
		$stmt->bindValue('autoassign', $autoassign);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('login', $hash->HashLogin($login));
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CLIENT_ACTIVE_UPDATE'), 1, $e->getMessage());
		}
		
		if ((int) $active == 1){
			$sql = 'DELETE FROM sessionhandler WHERE clientid = :id';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $id);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CLIENT_ACTIVE_UPDATE'), 1, $e->getMessage());
			}
		}
		return true;
	}

	public function editClientData ($Data, $id)
	{
		$sql = 'UPDATE clientdata SET 
					clientgroupid=:clientgroupid, 
					firstname=AES_ENCRYPT(:firstname, :encryptionKey), 
					surname=AES_ENCRYPT(:surname, :encryptionKey), 
					email=AES_ENCRYPT(:email, :encryptionKey),
					phone=AES_ENCRYPT(:phone, :encryptionKey),
					phone2=AES_ENCRYPT(:phone2, :encryptionKey),
					description=AES_ENCRYPT(:description, :encryptionKey)
				WHERE clientid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('clientgroupid', $Data['personal_data']['clientgroupid']);
		$stmt->bindValue('firstname', $Data['personal_data']['firstname']);
		$stmt->bindValue('surname', $Data['personal_data']['surname']);
		$stmt->bindValue('email', $Data['personal_data']['email']);
		$stmt->bindValue('phone', $Data['personal_data']['phone']);
		$stmt->bindValue('phone2', $Data['personal_data']['phone2']);
		$stmt->bindValue('description', $Data['additional_data']['description']);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CLIENTDATA_UPDATE'), 1, $e->getMessage());
		}
		
		$sendingo = App::getModel('sendingo');
		
		if (isset($Data['personal_data']['newsletter']) && $Data['personal_data']['newsletter'] == 1){
			if ($sendingo->emailExists($Data['personal_data']['email'], $Data['personal_data']['viewid'])){
				$sendingoId = $sendingo->activeEmail($Data['email'], $Data['personal_data']['viewid']);
			}
			else{
				$newId = $this->addClientAboutNewsletter($Data['personal_data']['email'], $Data['personal_data']['viewid']);
				if ($newId > 0){
					$sendingo->activeEmail($Data['personal_data']['email'], $Data['personal_data']['viewid']);
					$sendingoId = $sendingo->sendingoAddEmail($Data['personal_data']['email'], $Data['personal_data']['viewid']);
					$sendingo->updateSendingoId($Data['personal_data']['email'], $Data['personal_data']['viewid'], $sendingoId);
				}
			}
		}
		else{
			$sendingo->sendingoDeleteEmail($Data['personal_data']['email'], $Data['personal_data']['viewid']);
		}
		
		return true;
	}

	public function updateClientAddress ($Data, $id, $main)
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
					countryid	= :countryid
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
					countryid	= :countryid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->bindValue('clientid', $id);
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
		$stmt->bindValue('countryid', $Data['countryid']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		return true;
	}

	public function selectClientsFromCategory ($groups)
	{
		$Data = Array();
		foreach ($groups as $idgroup){
			$sql = "SELECT AES_DECRYPT(email, :encryptionkey) AS email
						FROM clientgroup CG
						LEFT JOIN clientdata CD ON CD.clientgroupid = CG.idclientgroup
						WHERE idclientgroup=:id";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $idgroup);
			$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[] = $rs['email'];
			}
		}
		return $Data;
	}

	public function selectClientGroup ($clients)
	{
		$Data = Array();
		foreach ($clients as $recipientlistid){
			$sql = "SELECT clientgroupid
						FROM recipientclientgrouplist
						WHERE recipientlistid=:recipientlistid";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('recipientlistid', $recipientlistid);
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[] = $rs['clientgroupid'];
			}
		}
		return $Data;
	}

	public function selectClient ($clients)
	{
		$Data = Array();
		foreach ($clients as $recipientlistid){
			$sql = "SELECT clientid
						FROM recipientclientlist
						WHERE recipientlistid=:recipientlistid";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('recipientlistid', $recipientlistid);
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[] = $rs['clientid'];
			}
		}
		return $Data;
	}

	public function selectClientNewsletter ($clients)
	{
		$Data = Array();
		foreach ($clients as $recipientlistid){
			$sql = "SELECT clientnewsletterid
						FROM recipientnewsletterlist
						WHERE recipientlistid=:recipientlistid";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('recipientlistid', $recipientlistid);
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[] = $rs['clientnewsletterid'];
			}
		}
		return $Data;
	}

	public function selectClientsGroupFromNewsletter ($clients)
	{
		$Data = Array();
		foreach ($clients as $clientgroupid){
			$sql = "SELECT AES_DECRYPT(email, :encryptionkey) AS email
						FROM clientdata
						WHERE clientgroupid=:clientgroupid";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientgroupid', $clientgroupid);
			$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[] = $rs['email'];
			}
		}
		return $Data;
	}

	public function selectClientsNewsletterFromNewsletter ($clients)
	{
		$Data = Array();
		foreach ($clients as $idclientnewsletter){
			$sql = "SELECT 
						email
					FROM clientnewsletter
					WHERE idclientnewsletter=:idclientnewsletter
					AND active=1";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('idclientnewsletter', $idclientnewsletter);
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[] = $rs['email'];
			}
		}
		return $Data;
	}

	public function selectClientsFromNewsletter ($clients)
	{
		$Data = Array();
		foreach ($clients as $clientid){
			$sql = "SELECT AES_DECRYPT(email, :encryptionkey) AS email
						FROM clientdata
						WHERE clientid=:clientid";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientid', $clientid);
			$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[] = $rs['email'];
			}
		}
		return $Data;
	}

	public function getClientMailAddress ($clientId)
	{
		$mail = '';
		$sql = "SELECT AES_DECRYPT(CD.email, :encryptionkey) AS email
					FROM clientdata CD
					LEFT JOIN client C ON CD.clientid=C.idclient
					WHERE C.idclient= :idclient";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->bindValue('idclient', $clientId);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$mail = $rs['email'];
			}
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return $mail;
	}

	public function getClientAddress ($id, $main)
	{
		$Data = Array(
			'idclientaddress' => '',
			'firstname' => '',
			'surname' => '',
			'companyname' => '',
			'nip' => '',
			'street' => '',
			'streetno' => '',
			'placeno' => '',
			'placename' => '',
			'postcode' => '',
			'countryid' => ''
		);
		
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
					countryid
				FROM clientaddress
				WHERE clientid=:clientid AND main = :main";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', $id);
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
					'countryid' => $rs['countryid']
				);
			}
		}
		catch (Exception $e){
			throw new FrontendException($this->trans('ERR_CLIENT_NO_EXIST'));
		}
		return $Data;
	}

	public function addClientAboutNewsletter ($email, $viewId = 0)
	{
		$sql = 'INSERT INTO clientnewsletter (email, viewid)
					VALUES (:email, :viewid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('email', $email);
		$stmt->bindValue('viewid', $viewId !== 0 ? $viewId : Helper::getViewId());
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		
		return Db::getInstance()->lastInsertId();
	}

	public function encryptEmail ($Data = Array())
	{
	}
}
