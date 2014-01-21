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
 * $Id: recipientlist.php 438 2011-08-27 09:29:36Z gekosale $
 */

namespace Gekosale;

class RecipientListModel extends Component\Model\Datagrid
{

	public function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('recipientlist', Array(
			'idrecipientlist' => Array(
				'source' => 'idrecipientlist'
			),
			'name' => Array(
				'source' => 'name'
			),
			'adddate' => Array(
				'source' => 'adddate'
			)
		));
		$datagrid->setFrom('
				recipientlist
			');
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getRecipientListForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteRecipientList ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteRecipientList'
		), $this->getName());
	}

	public function deleteRecipientList ($id)
	{
		DbTracker::deleteRows('recipientlist', 'idrecipientlist', $id);
	}

	public function addNewRecipient ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$reciepientlistid = $this->addNewRecipientName($Data);
			$this->addNewRecipientList($Data, $reciepientlistid);
			$this->addNewNewsletterList($Data, $reciepientlistid);
			$this->addNewClientgroupList($Data, $reciepientlistid);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_NEWSLETTER_ADD'), 112, $e->getMessage());
		}

		Db::getInstance()->commit();
		return $reciepientlistid;
	}

	public function addNewClientgroupList ($Data, $id)
	{
		foreach ($Data['clientgroup'] as $value){
			$sql = 'INSERT INTO recipientclientgrouplist (clientgroupid, recipientlistid)
						VALUES (:clientgroupid, :recipientlistid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientgroupid', $value);
			$stmt->bindValue('recipientlistid', $id);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CLIENT_GROUP_ADD'), 112, $e->getMessage());
			}
		}
	}

	public function addNewNewsletterList ($Data, $id)
	{
		foreach ($Data['clientnewsletter'] as $value){
			$sql = 'INSERT INTO recipientnewsletterlist (clientnewsletterid, recipientlistid)
						VALUES (:clientnewsletterid, :recipientlistid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientnewsletterid', $value);
			$stmt->bindValue('recipientlistid', $id);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CLIENT_NEWSLETTER_ADD'), 112, $e->getMessage());
			}
		}
	}

	public function addNewRecipientList ($Data, $id)
	{
		foreach ($Data['clients'] as $value){
			$sql = 'INSERT INTO recipientclientlist (clientid, recipientlistid)
						VALUES (:clientid, :recipientlistid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientid', $value);
			$stmt->bindValue('recipientlistid', $id);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CLIENT_NEWSLETTER_ADD'), 112, $e->getMessage());
			}
		}
	}

	public function addNewRecipientName ($Data)
	{
		$sql = 'INSERT INTO recipientlist (name)
					VALUES (:name)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);

		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_RECIPIENT_LIST_ADD'), 11, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function getRecipientListView ($idrecipientlist)
	{
		$sql = "SELECT
					idrecipientlist as id,
					name
				FROM recipientlist
				WHERE idrecipientlist = :idrecipientlist";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idrecipientlist', $idrecipientlist);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'name' => $rs['name'],
				'clientgrouplist' => $this->getRecipientClientGroupList($rs['id']),
				'clientlist' => $this->getRecipientClientList($rs['id']),
				'clientnewsletterlist' => $this->getClientNewsletterList($rs['id'])
			);
		}
		else{
			throw new CoreException($this->trans('ERR_RECIPIENT_LIST_NO_EXIST'));
		}
		return $Data;
	}

	public function getRecipientClientGroupList ($recipientlistid)
	{
		$sql = "SELECT
					clientgroupid
				FROM recipientclientgrouplist
				WHERE recipientlistid = :recipientlistid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('recipientlistid', $recipientlistid);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$clientgroupid = $rs['clientgroupid'];
			$Data[$clientgroupid] = Array(
				'clientgroupid' => $rs['clientgroupid']
			);
		}
		return $Data;
	}

	public function getRecipientClientList ($recipientlistid)
	{
		$sql = "SELECT
					clientid
				FROM recipientclientlist
				WHERE recipientlistid = :recipientlistid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('recipientlistid', $recipientlistid);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$clientid = $rs['clientid'];
			$Data[$clientid] = Array(
				'clientid' => $rs['clientid']
			);
		}
		return $Data;
	}

	public function getClientNewsletterList ($recipientlistid)
	{
		$sql = "SELECT
					clientnewsletterid
				FROM recipientnewsletterlist
				WHERE recipientlistid = :recipientlistid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('recipientlistid', $recipientlistid);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$clientnewsletterid = $rs['clientnewsletterid'];
			$Data[$clientnewsletterid] = Array(
				'clientnewsletterid' => $rs['clientnewsletterid']
			);
		}
		return $Data;
	}

	public function editRecipientList ($Data, $recipientListId)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->updateRecipientList($Data, $recipientListId);
			$this->updateClientGroupList($Data, $recipientListId);
			$this->updateClientsList($Data, $recipientListId);
			$this->updateRecipientNewsletterList($Data, $recipientListId);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_USER_UPDATE'), 118, $e - getMessage());
		}

		Db::getInstance()->commit();
	}

	public function updateRecipientList ($Data, $recipientlistid)
	{
		$sql = 'UPDATE recipientlist SET name=:name WHERE idrecipientlist = :recipientlistid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);

		$stmt->bindValue('recipientlistid', $recipientlistid);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_RECEPIENTLIST_EDIT'), 13, $e->getMessage());
			return false;
		}
	}

	public function updateClientGroupList ($Data, $id)
	{
		DbTracker::deleteRows('recipientclientgrouplist', 'recipientlistid', $id);

		if ( !empty($Data['clientgroup']) && is_array($Data['clientgroup'])) {
			foreach ($Data['clientgroup'] as $key => $val){
				$sql = 'INSERT INTO recipientclientgrouplist (clientgroupid, recipientlistid)
						VALUES (:clientgroupid, :recipientlistid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('clientgroupid', $val);
				$stmt->bindValue('recipientlistid', $id);

				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_RECIPIENT_CLIENT_GROUP_LIST_EDIT'), 13, $e->getMessage());
				}
			}
		}

		return true;
	}

	public function updateClientsList ($Data, $id)
	{
		DbTracker::deleteRows('recipientclientlist', 'recipientlistid', $id);

		if ( !empty($Data['clients']) && is_array($Data['clients'])) {
			foreach ($Data['clients'] as $key => $val){
				$sql = 'INSERT INTO recipientclientlist (clientid, recipientlistid)
						VALUES (:clientid, :recipientlistid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('clientid', $val);
				$stmt->bindValue('recipientlistid', $id);

				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_RECIPIENT_CLIENT_LIST_EDIT'), 13, $e->getMessage());
				}
			}
		}

		return true;
	}

	public function updateRecipientNewsletterList ($Data, $id)
	{
		DbTracker::deleteRows('recipientnewsletterlist', 'recipientlistid', $id);

		foreach ($Data['clientnewsletter'] as $key => $val){
			$sql = 'INSERT INTO recipientnewsletterlist (clientnewsletterid, recipientlistid)
					VALUES (:clientnewsletterid, :recipientlistid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientnewsletterid', $val);
			$stmt->bindValue('recipientlistid', $id);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_RECIPIENT_NEWSLETTER_LIST_EDIT'), 13, $e->getMessage());
			}
		}
		return true;
	}
}