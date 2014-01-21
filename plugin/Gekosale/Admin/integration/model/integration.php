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
 * $Revision: 552 $
 * $Author: gekosale $
 * $Date: 2011-10-08 17:56:59 +0200 (So, 08 paź 2011) $
 * $Id: integration.php 552 2011-10-08 15:56:59Z gekosale $ 
 */

namespace Gekosale;

class IntegrationModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('integration', Array(
			'idintegration' => Array(
				'source' => 'idintegration'
			),
			'name' => Array(
				'source' => 'name',
				'prepareForAutosuggest' => true
			),
			'symbol' => Array(
				'source' => 'symbol'
			)
		));
		
		$datagrid->setFrom('
			integration
		');
	}

	public function getNameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
	}

	public function getControllerForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('controller', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getIntegrationForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function getIntegrationModelById ($id)
	{
		$sql = 'SELECT symbol FROM integration WHERE idintegration = :idintegration';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idintegration', $id);
		$stmt->execute();
		$controller = null;
		while ($rs = $stmt->fetch()){
			$controller = $rs['symbol'];
		}
		return $controller;
	}

	public function getIntegrationModelAll ()
	{
		$sql = 'SELECT name, symbol FROM integration ';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'name' => $rs['name'],
				'model' => $rs['symbol']
			);
		}
		return $Data;
	}

	public function getIntegrationView ($id)
	{
		$sql = "SELECT * FROM integrationwhitelist
				WHERE integrationid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array(
			'whitelist' => Array()
		);
		while ($rs = $stmt->fetch()){
			$Data['whitelist']['ip'][] = $rs['ipaddress'];
		}
		return $Data;
	}

	public function editIntegration ($Data, $id)
	{
		DbTracker::deleteRows('integrationwhitelist', 'integrationid', $id);
		
		foreach ($Data['ip'] as $key => $value){
			$sql = 'INSERT INTO integrationwhitelist (integrationid, ipaddress)
					VALUES (:integrationid, :ipaddress)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('integrationid', $id);
			$stmt->bindValue('ipaddress', $value);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_POLL_ANSWERS_ADD'), 1225, $e->getMessage());
			}
		}
		return $Data;
	}
}