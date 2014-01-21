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
 * $Id: clientnewsletter.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;

class ClientNewsletterModel extends Component\Model\Datagrid
{

	public function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('clientnewsletter', Array(
			'idclientnewsletter' => Array(
				'source' => 'idclientnewsletter'
			),
			'email' => Array(
				'source' => 'email'
			),
			'adddate' => Array(
				'source' => 'adddate'
			),
			'active' => Array(
				'source' => 'IF( `active` = 1, \'TXT_ACTIVE\', \'TXT_INACTIVE\')',
				'processLanguage' => true
			)
		));
		$datagrid->setFrom('
				clientnewsletter 
			');
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getClientNewsletterForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteClientNewsletter ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteClientNewsletter'
		), $this->getName());
	}

	public function deleteClientNewsletter ($id)
	{
		DbTracker::deleteRows('clientnewsletter', 'idclientnewsletter', $id);
	}

	public function doAJAXEnableClientNewsletter ($datagridId, $id)
	{
		try{
			$this->enableClientNewsletter($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_ENABLE_USER')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function doAJAXDisableClientNewsletter ($datagridId, $id)
	{
		try{
			$this->disableClientNewsletter($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_DISABLE_USER')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function disableClientNewsletter ($id)
	{
		$sql = 'UPDATE clientnewsletter SET active = 0 WHERE idclientnewsletter = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function enableClientNewsletter ($id)
	{
		$sql = 'UPDATE clientnewsletter SET active = 1 WHERE idclientnewsletter = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function getClientsToSelect ()
	{
		$Data = $this->getClientsNewsletterAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['email'];
		}
		return $tmp;
	}

	public function getClientsNewsletterAll ()
	{
		$sql = 'SELECT idclientnewsletter as id, email FROM clientnewsletter WHERE active=1';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'email' => $rs['email']
			);
		}
		return $Data;
	}

	public function getClientAll ()
	{
		$rs = $this->registry->db->executeQuery('SELECT idclientnewsletter AS id, email FROM clientnewsletter');
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'email' => $rs['email']
			);
		}
		return $Data;
	}

	public function getClientAlltoSelect ()
	{
		$Data = $this->getClientAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['email'];
		}
		return $tmp;
	}

	public function getGroupsToSelect ()
	{
		$Data = $this->getClientGroupsAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return $tmp;
	}

	public function getClientGroupsAll ()
	{
		$sql = 'SELECT idclientgroupnewsletter as id, CG.name 
					FROM clientgroupnewsletter
					LEFT JOIN clientgroup CG ON CG.idclientgroup = clientgroupid
					GROUP BY name';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $rs['name']
			);
		}
		return $Data;
	}
}