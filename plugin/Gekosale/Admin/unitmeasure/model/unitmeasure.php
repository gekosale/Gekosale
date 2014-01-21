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
 * 
 * $Revision: 114 $
 * $Author: gekosale $
 * $Date: 2011-05-07 18:41:26 +0200 (So, 07 maj 2011) $
 * $Id: unitmeasure.php 114 2011-05-07 16:41:26Z gekosale $ 
 */

namespace Gekosale;
use FormEngine;

class UnitMeasureModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('unitmeasure', Array(
			'idunitmeasure' => Array(
				'source' => 'U.idunitmeasure'
			),
			'name' => Array(
				'source' => 'UT.name'
			)
		));
		
		$datagrid->setFrom('
			unitmeasure U
			LEFT JOIN unitmeasuretranslation UT ON U.idunitmeasure = UT.unitmeasureid AND UT.languageid = :languageid
		');
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getUnitMeasureForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteUnitMeasure ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteUnitMeasure'
		), $this->getName());
	}

	public function deleteUnitMeasure ($id)
	{
		DbTracker::deleteRows('unitmeasure', 'idunitmeasure', $id);
	}

	public function addUnitMeasure ($Data)
	{
		$sql = 'INSERT INTO unitmeasure (adddate) VALUES (NOW())';
		$stmt = Db::getInstance()->prepare($sql);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_UNIT_MEASURE_ADD'), 11, $e->getMessage());
		}
		
		$unitmeasureid = Db::getInstance()->lastInsertId();
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO unitmeasuretranslation (unitmeasureid, name, languageid)
					VALUES (:unitmeasureid, :name, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('unitmeasureid', $unitmeasureid);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_UNIT_MEASURE_ADD'), 11, $e->getMessage());
			}
		}
		return $unitmeasureid;
	}

	public function editUnitMeasure ($Data, $id)
	{
		DbTracker::deleteRows('unitmeasuretranslation', 'unitmeasureid', $id);
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO unitmeasuretranslation (unitmeasureid, name, languageid)
					VALUES (:unitmeasureid, :name, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('unitmeasureid', $id);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_UNIT_MEASURE_ADD'), 11, $e->getMessage());
			}
		}
		return true;
	}

	public function getUnitMeasureAll ()
	{
		$sql = 'SELECT 
					U.idunitmeasure AS id,
					UT.name
				FROM unitmeasure U
				LEFT JOIN unitmeasuretranslation UT ON UT.unitmeasureid = U.idunitmeasure AND UT.languageid = :language';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('language', Helper::getLanguageId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $rs['name']
			);
		}
		return $Data;
	}

	public function getUnitMeasureToSelect ()
	{
		$Data = $this->getUnitMeasureAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return $tmp;
	}

	public function getUnitMeasureAsExchangeOptions ()
	{
		$Data = $this->getUnitMeasureAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[] = Array(
				'sValue' => $key['id'],
				'sLabel' => $key['name']
			);
		}
		return $tmp;
	}

	public function getUnitMeasureView ($id)
	{
		$sql = "SELECT idunitmeasure AS id FROM unitmeasure WHERE idunitmeasure = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$rs = $stmt->execute();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'language' => $this->getUnitMeasureTranslation($id),
				'id' => $rs['id']
			);
		}
		else{
			throw new CoreException($this->trans('ERR_UNIT_MEASURE_NO_EXIST'));
		}
		return $Data;
	}

	public function addEmptyUnitMeasure ($request)
	{
		$sql = 'SELECT unitmeasureid FROM unitmeasuretranslation WHERE name = :name AND languageid = :languageid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $request['name']);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$rs = $stmt->fetch();
		
		if ($rs){
			$id = $rs['producerid'];
		}
		else{
			
			$sql = 'INSERT INTO unitmeasure (adddate) VALUES (NOW())';
			$stmt = Db::getInstance()->prepare($sql);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_UNIT_MEASURE_ADD'), 11, $e->getMessage());
			}
			
			$id = Db::getInstance()->lastInsertId();
			
			$sql = 'INSERT INTO unitmeasuretranslation (unitmeasureid, name, languageid)
					VALUES (:unitmeasureid, :name, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('unitmeasureid', $id);
			$stmt->bindValue('name', $request['name']);
			$stmt->bindValue('languageid', Helper::getLanguageId());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_UNIT_MEASURE_ADD'), 11, $e->getMessage());
			}
		}
		
		return Array(
			'id' => $id,
			'options' => $this->getUnitMeasureAsExchangeOptions()
		);
	}

	public function getUnitMeasureTranslation ($id)
	{
		$sql = "SELECT 
					name, 
					languageid
				FROM unitmeasuretranslation
				WHERE unitmeasureid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$rs = $stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['languageid']] = Array(
				'name' => $rs['name']
			);
		}
		return $Data;
	}
}