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
 * $Id: contact.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Plugin;

class ContactModel extends Component\Model
{
	protected $contactList;

	public function __construct ($registry, $modelFile)
	{
		$this->contactList = $this->getContactList();
	}

	public function getContactById ($id)
	{
		return isset($this->contactList[$id]) ? $this->contactList[$id] : Array();
	}

	public function getContactToSelect ()
	{
		$Data = $this->getContactList();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['idcontact']] = $key['name'];
		}
		return $tmp;
	}

	public function getDepartmentMail ($idcontact)
	{
		$sql = 'SELECT
					email 
				FROM contacttranslation 
				WHERE contactid = :idcontact AND languageid = :languageid';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idcontact', $idcontact);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$email = $rs['email'];
		}
		return $email;
	}

	public function getContactList ()
	{
		$sql = "SELECT
					C.idcontact,
					CT.name, 
					CT.email, 
					CT.phone, 
					CT.fax, 
					CT.street, 
					CT.streetno, 
					CT.placeno, 
					CT.placename, 
					CT.postcode,
					CT.businesshours
				FROM contact C
				LEFT JOIN contacttranslation CT ON CT.contactid = C.idcontact AND CT.languageid = :languageid
				LEFT JOIN contactview CV ON CV.contactid = C.idcontact
				WHERE C.publish = 1 AND CV.viewid = :viewid
				ORDER BY C.idcontact";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$Data = Array();
		try{
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[$rs['idcontact']] = Array(
					'idcontact' => $rs['idcontact'],
					'name' => $rs['name'],
					'phone' => $rs['phone'],
					'fax' => $rs['fax'],
					'email' => $rs['email'],
					'street' => $rs['street'],
					'streetno' => $rs['streetno'],
					'placeno' => $rs['placeno'],
					'placename' => $rs['placename'],
					'postcode' => $rs['postcode'],
					'businesshours' => $rs['businesshours']
				);
			}
		}
		catch (Exception $e){
			throw new FrontendException($fe->getMessage('ERR_QUERY_WISHLIST'));
		}
		return $Data;
	}
}