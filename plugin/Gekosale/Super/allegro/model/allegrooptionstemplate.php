<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms 
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the 
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace Gekosale;

class AllegroOptionsTemplateModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('allegrooptionstemplate', Array(
			'idallegrooptionstemplate' => Array(
				'source' => 'idallegrooptionstemplate'
			),
			'name' => Array(
				'source' => 'name'
			)
		));
		
		$datagrid->setFrom('
				allegrooptionstemplate
		');
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getAllegrooptionstemplateForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteAllegrooptionstemplate ($datagrid, $id)
	{
		return $this->getDatagrid()->deleteRow($datagrid, $id, Array(
			$this,
			'deleteAllegrooptionstemplate'
		), $this->getName());
	}

	public function deleteAllegrooptionstemplate ($id)
	{
		DbTracker::deleteRows('allegrooptionstemplate', 'idallegrooptionstemplate', $id);
	}

	public function getAllegrooptionstemplateAll ()
	{
		$sql = 'SELECT idallegrooptionstemplate AS id, name
				FROM allegrooptionstemplate';
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

	public function getAllegrooptionstemplateById ($id)
	{
		$sql = 'SELECT * FROM allegrooptionstemplate WHERE idallegrooptionstemplate = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			return unserialize($rs['data']);
		}
	}

	public function getAllegrooptionstemplateToSelect ()
	{
		$Data = $this->getAllegrooptionstemplateAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return $tmp;
	}

	public function addNewAllegroOptionsTemplate ($submitedData)
	{
		try{
			$idNewTemplate = $this->addAllegroOptionsTemplate($submitedData);
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return true;
	}

	public function addAllegroOptionsTemplate ($name, $data)
	{
		$sql = "INSERT INTO allegrooptionstemplate (name, data)
				VALUES (:name, :data)";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $name);
		$stmt->bindValue('data', serialize($data));
		$stmt->execute();
	}
	
	public function editAllegroOptionsTemplate ($name, $data, $id)
	{
		$sql = "UPDATE allegrooptionstemplate SET name = :name, data = :data WHERE idallegrooptionstemplate = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('name', $name);
		$stmt->bindValue('data', serialize($data));
		$stmt->execute();
	}
}