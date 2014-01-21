<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2011 Gekosale. Zabronione jest usuwanie informacji o
 * licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 279 $
 * $Author: gekosale $
 * $Date: 2011-07-28 23:13:43 +0200 (Cz, 28 lip 2011) $
 * $Id: product.php 279 2011-07-28 21:13:43Z gekosale $
 */
namespace Gekosale;

class ClientsModel extends Component\Model
{

	public function getClients ($request = Array())
	{
		$startingfrom = (isset($request['starting_from'])) ? $request['starting_from'] : 0;
		
		$sql = "SELECT 
					idclient
				FROM `client` 
				WHERE idclient > :starting_from";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('starting_from', $startingfrom);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['idclient']
			);
		}
		return $Data;
	}

	public function getClient ($id)
	{
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
		$stmt->bindValue('clientid', $id);
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
					'countryid' => $rs['countryid'],
					'billing_address' => $this->getClientAddress($id, 1),
					'shipping_address' => $this->getClientAddress($id, 0)
				);
				return $Data;
			}
		}
		catch (Exception $e){
			throw new FrontendException($this->trans('ERR_CLIENT_NO_EXIST'));
		}
	}

	public function getClientAddress ($id, $main)
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
		$stmt->bindValue('clientid', $id);
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
} 