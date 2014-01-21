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
 * $Revision: 464 $
 * $Author: gekosale $
 * $Date: 2011-08-31 08:19:48 +0200 (Śr, 31 sie 2011) $
 * $Id: contact.php 464 2011-08-31 06:19:48Z gekosale $ 
 */

namespace Gekosale;

class ContactModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('contact', Array(
			'idcontact' => Array(
				'source' => 'C.idcontact'
			),
			'name' => Array(
				'source' => 'CT.name',
				'prepareForAutosuggest' => true
			),
			'email' => Array(
				'source' => 'CT.email'
			),
			'phone' => Array(
				'source' => 'CT.phone'
			),
			'fax' => Array(
				'source' => 'CT.fax'
			),
			'address' => Array(
				'source' => 'CONCAT(CT.street, \' \', CT.streetno, \' \', CT.placeno, \', \', CT.postcode, \' \', CT.placename)'
			),
			'street' => Array(
				'source' => 'CT.street',
				'prepareForAutosuggest' => true
			),
			'streetno' => Array(
				'source' => 'CT.streetno'
			),
			'placeno' => Array(
				'source' => 'CT.placeno'
			),
			'postcode' => Array(
				'source' => 'CT.postcode'
			),
			'placename' => Array(
				'source' => 'CT.placename',
				'prepareForAutosuggest' => true
			)
		));
		
		$datagrid->setFrom('
			contact C
			LEFT JOIN contacttranslation CT ON CT.contactid = C.idcontact AND CT.languageid = :languageid
			LEFT JOIN contactview CV ON CV.contactid = C.idcontact
		');
		
		$datagrid->setAdditionalWhere('
			CV.viewid IN (' . Helper::getViewIdsAsString() . ')
		');
		
		$datagrid->setGroupBy('C.idcontact');
	}

	public function getNameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
	}

	public function getStreetForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('street', $request, $processFunction);
	}

	public function getPlacenameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('placename', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getContactForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteContact ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteContact'
		), $this->getName());
	}

	public function deleteContact ($id)
	{
		DbTracker::deleteRows('contact', 'idcontact', $id);
	}

	public function getContactView ($id)
	{
		$sql = "SELECT idcontact AS id,	publish 
				FROM contact 
				WHERE idcontact = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'id' => $rs['id'],
				'publish' => $rs['publish'],
				'language' => $this->getContactTranslation($id),
				'view' => $this->getContactViews($id)
			);
		}
		return $Data;
	}

	public function getContactTranslation ($id)
	{
		$sql = "SELECT 
					name, 
					email, 
					phone, 
					fax, 
					street, 
					streetno, 
					placeno, 
					placename, 
					postcode, 
					languageid,
					countryid,
					businesshours
				FROM contacttranslation
				WHERE contactid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['languageid']] = Array(
				'name' => $rs['name'],
				'email' => $rs['email'],
				'phone' => $rs['phone'],
				'fax' => $rs['fax'],
				'street' => $rs['street'],
				'streetno' => $rs['streetno'],
				'placeno' => $rs['placeno'],
				'placename' => $rs['placename'],
				'postcode' => $rs['postcode'],
				'countryid' => $rs['countryid'],
				'businesshours' => $rs['businesshours']
			);
		}
		return $Data;
	}
	
	public function getContactToSelect ()
	{
		$sql = "SELECT 
					name,
					contactid
				FROM contacttranslation
				WHERE languageid = :languageid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['contactid']] = $rs['name'];
		}
		return $Data;
	}

	public function getContactViews ($id)
	{
		$sql = "SELECT viewid FROM contactview WHERE contactid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['viewid'];
		}
		return $Data;
	}

	public function editContact ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->updateContact($Data['publish'], $id);
			$this->updateContactTranslation($Data, $id);
			$this->updateContactView($Data['view'], $id);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CONTACT_EDIT'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function updateContact ($publish, $id)
	{
		$sql = 'UPDATE contact SET 
					publish = :publish
				WHERE idcontact = :id';
		$stmt = Db::getInstance()->prepare($sql);
		if (! empty($publish)){
			$stmt->bindValue('publish', $publish);
		}
		else{
			$stmt->bindValue('publish', 0);
		}
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CONTACT_EDIT'), 13, $e->getMessage());
		}
	}

	public function updateContactTranslation ($Data, $id)
	{
		DbTracker::deleteRows('contacttranslation', 'contactid', $id);
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO contacttranslation SET
						contactid = :contactid,
						name = :name, 
						email = :email, 
						street = :street,
						streetno = :streetno,
						placeno = :placeno,
						fax = :fax,
						phone = :phone,
						placename = :placename,
						postcode = :postcode,
						languageid = :languageid,
						countryid = :countryid,
						businesshours = :businesshours';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('contactid', $id);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('email', $Data['email'][$key]);
			$stmt->bindValue('street', $Data['street'][$key]);
			$stmt->bindValue('streetno', $Data['streetno'][$key]);
			$stmt->bindValue('placeno', $Data['placeno'][$key]);
			$stmt->bindValue('fax', $Data['fax'][$key]);
			$stmt->bindValue('phone', $Data['phone'][$key]);
			$stmt->bindValue('placename', $Data['placename'][$key]);
			$stmt->bindValue('postcode', $Data['postcode'][$key]);
			$stmt->bindValue('businesshours', $Data['businesshours'][$key]);
			$stmt->bindValue('countryid', $Data['countryid'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CONTACT_TRANSLATION_EDIT'), 13, $e->getMessage());
			}
		}
	}

	public function updateContactView ($array, $id)
	{
		DbTracker::deleteRows('contactview', 'contactid', $id);
		
		if (is_array($array) && ! empty($array)){
			foreach ($array as $key => $val){
				$sql = 'INSERT INTO contactview (contactid,viewid)
						VALUES (:contactid, :viewid)';
				$stmt = Db::getInstance()->prepare($sql);
				
				$stmt->bindValue('contactid', $id);
				$stmt->bindValue('viewid', $val);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_CONTACT_VIEW_EDIT'), 4, $e->getMessage());
				}
			}
		}
	}

	public function addNewContact ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newContactId = $this->addContact($Data['publish']);
			if (is_array($Data['view']) && ! empty($Data['view'])){
				$this->addContactView($Data['view'], $newContactId);
			}
			$this->addContactTranslation($Data, $newContactId);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CONTACT_ADD'), 11, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function addContact ($publish)
	{
		$sql = 'INSERT INTO contact 
				SET	publish = :publish';
		$stmt = Db::getInstance()->prepare($sql);
		if (! empty($publish)){
			$stmt->bindValue('publish', $publish);
		}
		else{
			$stmt->bindValue('publish', 0);
		}
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CONTACT_ADD'), 11, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addContactView ($array, $id)
	{
		foreach ($array as $value){
			$sql = 'INSERT INTO contactview 
					SET	contactid = :contactid,
					viewid = :viewid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('contactid', $id);
			$stmt->bindValue('viewid', $value);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CONTACT_VIEW_ADD'), 4, $e->getMessage());
			}
		}
	}

	public function addContactTranslation ($Data, $id)
	{
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO contacttranslation SET
						contactid = :contactid,
						name = :name, 
						email = :email, 
						street = :street,
						streetno = :streetno,
						placeno = :placeno,
						fax = :fax,
						phone = :phone,
						placename = :placename,
						postcode = :postcode,
						languageid = :languageid,
						countryid = :countryid,
						businesshours = :businesshours';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('contactid', $id);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('email', $Data['email'][$key]);
			$stmt->bindValue('street', $Data['street'][$key]);
			$stmt->bindValue('streetno', $Data['streetno'][$key]);
			$stmt->bindValue('placeno', $Data['placeno'][$key]);
			$stmt->bindValue('fax', $Data['fax'][$key]);
			$stmt->bindValue('phone', $Data['phone'][$key]);
			$stmt->bindValue('placename', $Data['placename'][$key]);
			$stmt->bindValue('postcode', $Data['postcode'][$key]);
			$stmt->bindValue('businesshours', $Data['businesshours'][$key]);
			$stmt->bindValue('countryid', $Data['countryid'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CONTACT_TRANSLATION_ADD'), 13, $e->getMessage());
			}
		}
	}
}