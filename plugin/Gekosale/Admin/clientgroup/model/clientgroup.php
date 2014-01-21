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
 * $Id: clientgroup.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale;

class ClientGroupModel extends Component\Model\Datagrid
{

	public function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('clientgroup', Array(
			'idclientgroup' => Array(
				'source' => 'CG.idclientgroup'
			),
			'name' => Array(
				'source' => 'CGT.name',
				'prepareForAutosuggest' => true
			),
			'clientcount' => Array(
				'source' => 'COUNT(CD.clientid)',
				'filter' => 'having'
			)
		));
		
		$datagrid->setFrom('
			`clientgroup` CG
			LEFT JOIN clientgrouptranslation CGT ON CG.idclientgroup = CGT.clientgroupid AND CGT.languageid = :languageid
			LEFT JOIN `clientdata` CD ON CD.clientgroupid = CG.idclientgroup
		');
		
		$datagrid->setGroupBy('
			CG.idclientgroup
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

	public function getClientGroupForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteClientGroup ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteClientGroup'
		), $this->getName());
	}

	public function deleteClientGroup ($id)
	{
		DbTracker::deleteRows('clientgroup', 'idclientgroup', $id);
	}

	public function ClientGroup ($Data)
	{
		$sql = 'INSERT INTO clientgroup (adddate) VALUES (NOW())';
		$stmt = Db::getInstance()->prepare($sql);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CLIENTGROUP_ADD'), 9, $e->getMessage());
		}
		
		$id = Db::getInstance()->lastInsertId();
		
		DbTracker::deleteRows('clientgrouptranslation', 'clientgroupid', $id);
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO clientgrouptranslation (clientgroupid, name, languageid)
						VALUES (:clientgroupid, :name, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientgroupid', $id);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CLIENTGROUP_EDIT'), 10, $e->getMessage());
			}
		}
		
		return $id;
	}

	public function addClientGroup ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newClientGroupId = $this->ClientGroup($Data);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_GROUP_ADD'));
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function getClientGroupById ($id)
	{
		$sql = 'SELECT CG.idclientgroup AS id
					FROM clientgroup CG
					WHERE idclientgroup= :id';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data = Array(
				'language' => $this->getClientGroupTranslation($id),
				'id' => $rs['id'],
				'clients' => $this->getClientGroupClients($id)
			);
		}
		else{
			throw new CoreException($this->trans('ERR_GROUP_NO_EXIST'));
		}
		return $Data;
	}

	public function getClientGroupClients ($id)
	{
		$sql = "SELECT clientid
				FROM clientdata
				WHERE clientgroupid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['clientid'];
		}
		return $Data;
	}

	public function getClientGroupTranslation ($id)
	{
		$sql = "SELECT name,languageid
					FROM clientgrouptranslation
					WHERE clientgroupid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['languageid']] = Array(
				'name' => $rs['name']
			);
		}
		return $Data;
	}

	public function editClientGroup ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		
		DbTracker::deleteRows('clientgrouptranslation', 'clientgroupid', $id);
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO clientgrouptranslation (clientgroupid, name, languageid)
						VALUES (:clientgroupid, :name, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientgroupid', $id);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CLIENTGROUP_EDIT'), 10, $e->getMessage());
			}
		}
		
		foreach ($Data['clients'] as $key => $val){
			$sql = 'UPDATE clientdata SET clientgroupid = :clientgroupid WHERE clientid = :clientid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientgroupid', $id);
			$stmt->bindValue('clientid', $val);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CLIENTGROUP_EDIT'), 10, $e->getMessage());
			}
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function getClientGroupAll ()
	{
		$sql = 'SELECT clientgroupid AS id, name
				FROM clientgrouptranslation 
				WHERE languageid= :languageid
				ORDER BY name ASC';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
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

	public function getClientGroupAsExchangeOptions ()
	{
		$Data = $this->getClientGroupAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[] = Array(
				'sValue' => $key['id'],
				'sLabel' => $key['name']
			);
		}
		return $tmp;
	}

	public function addEmptyClientGroup ($request)
	{
		$sql = 'SELECT clientgroupid FROM clientgrouptranslation WHERE name = :name AND languageid = :languageid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $request['name']);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$rs = $stmt->fetch();
		
		if ($rs){
			$id = $rs['clientgroupid'];
		}
		else{
			
			$sql = 'INSERT INTO clientgroup (adddate) VALUES (NOW())';
			$stmt = Db::getInstance()->prepare($sql);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CLIENTGROUP_ADD'), 9, $e->getMessage());
			}
			
			$id = Db::getInstance()->lastInsertId();
			
			DbTracker::deleteRows('clientgrouptranslation', 'clientgroupid', $id);
			
			$sql = 'INSERT INTO clientgrouptranslation (clientgroupid, name, languageid)
					VALUES (:clientgroupid, :name, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientgroupid', $id);
			$stmt->bindValue('name', $request['name']);
			$stmt->bindValue('languageid', Helper::getLanguageId());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CLIENTGROUP_EDIT'), 10, $e->getMessage());
			}
		}
		
		return Array(
			'id' => $id,
			'options' => $this->getClientGroupAsExchangeOptions()
		);
	}

	public function getClientGroupAllToSelect ()
	{
		$Data = $this->getClientGroupAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return $tmp;
	}

	public function clientGroupClients ($id)
	{
		$sql = 'SELECT 
						AES_DECRYPT(firstname, :encryptionkey) AS firstname,
						AES_DECRYPT(surname, :encryptionkey) AS surname, 
						idclientgroup as id
					FROM clientdata
					LEFT JOIN clientgroup ON idclientgroup = clientgroupid
					WHERE clientgroupid= :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function getClientGroupToRangeEditor ()
	{
		$sql = 'SELECT clientgroupid AS id, name
					FROM clientgrouptranslation 
					WHERE languageid= :languageid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = $rs['name'];
		}
		return $Data;
	}

	public function getAssignToGroupPerView ($viewid)
	{
		$sql = 'SELECT `from`, `to`, clientgroupid
					FROM assigntogroup 
					WHERE viewid= :viewid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', $viewid);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data['ranges'][] = Array(
				'min' => $rs['from'],
				'max' => $rs['to'],
				'price' => $rs['clientgroupid']
			);
		}
		return $Data;
	}
}