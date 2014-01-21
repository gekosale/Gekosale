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
 * $Id: productstatus.php 438 2011-08-27 09:29:36Z gekosale $
 */

namespace Gekosale;

class ProductStatusModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('productstatus', Array(
			'idproductstatus' => Array(
				'source' => 'PS.idproductstatus'
			),
			'name' => Array(
				'source' => 'PS.name'
			),
			'adddate' => Array(
				'source' => 'PS.adddate'
			)
		));

		$datagrid->setFrom('
			`productstatus` PS
		');

		$datagrid->setGroupBy('
			PS.idproductstatus
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

	public function getProductstatusForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function getProductstatusView ($id)
	{
		$sql = "SELECT
					idproductstatus AS id,
					name,
					symbol
				FROM productstatus
				WHERE idproductstatus =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'id' => $rs['id'],
				'name' => $rs['name'],
				'symbol' => $rs['symbol'],
			);
		}
		return $Data;
	}

	public function getProductstatusAll ($clean = true)
	{
		$sql = "SELECT
					idproductstatus AS id, name
				FROM productstatus
				ORDER BY name ASC";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$Data = Array();
		if ($clean){
			$Data[0] = $this->trans('TXT_CLEAR');
		}
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = $rs['name'];

		}
		return $Data;
	}

	public function getProductstatusAllToFilter ()
	{
		$statuses = $this->getProductstatusAll(false);

		$Data[0] = Array(
			'id' => '',
			'caption' => ''
		);

		foreach ($statuses as $id => $name){
			$Data[] = Array(
				'id' => $name,
				'caption' => $name
			);
		}
		return $Data;
	}

	public function getProductstatusAsExchangeOptions ()
	{
		$statuses = $this->getProductstatusAll(false);
		$Data = Array();
		foreach ($statuses as $id => $name){
			$Data[] = Array(
				'sValue' => $id,
				'sLabel' => $name
			);
		}
		return $Data;
	}

	public function doAJAXDeleteProductStatus ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteProductStatus'
		), $this->getName());
	}

	public function deleteProductStatus ($id)
	{
		DbTracker::deleteRows('productstatus', 'idproductstatus', $id);
	}

	public function addNewProductStatus ($Data)
	{
		$sql = 'INSERT INTO productstatus (name, symbol) VALUES (:name, :symbol)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);
		$stmt->bindValue('symbol', empty($Data['symbol']) ? '' : $Data['symbol']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_ORDERSTATUS_ADD'), 11, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function editProductStatus ($Data, $id)
	{
		$sql = 'UPDATE productstatus set name = :name, symbol = :symbol WHERE idproductstatus = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);
		$stmt->bindValue('symbol', $Data['symbol']);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_ORDERSTATUS_ADD'), 11, $e->getMessage());
		}

	}
}