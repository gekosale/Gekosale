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
 * $Id: rangetype.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;

class RangeTypeModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('rangetype', Array(
			'idrangetype' => Array(
				'source' => 'RT.idrangetype'
			),
			'name' => Array(
				'source' => 'RTT.name',
			),
			'categoryname' => Array(
				'source' => 'CT.name'
			),
			'categoryid' => Array(
				'source' => 'RTC.categoryid',
				'prepareForTree' => true,
				'first_level' => App::getModel('product')->getCategories()
			),
			'ancestorcategoryid' => Array(
				'source' => 'CP.ancestorcategoryid'
			),
			'categoriesname' => Array(
				'source' => 'GROUP_CONCAT(DISTINCT SUBSTRING(CONCAT(\' \', CT.name), 1))',
				'filter' => 'having'
			)
		));
		
		$datagrid->setFrom('
			rangetype RT
			LEFT JOIN rangetypecategory RTC ON RTC.rangetypeid = RT.idrangetype
			LEFT JOIN rangetypetranslation RTT ON RTT.rangetypeid = RT.idrangetype AND RTT.languageid = :languageid
			LEFT JOIN category C ON C.idcategory = RTC.categoryid
			LEFT JOIN categorypath CP ON C.idcategory = CP.categoryid
			LEFT JOIN categorytranslation CT ON C.idcategory = CT.categoryid AND CT.languageid = :languageid
		');
		
		$datagrid->setGroupBy('
			RT.idrangetype
		');
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getRangeTypeForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteRangeType ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteRangeType'
		), $this->getName());
	}

	public function deleteRangeType ($id)
	{
		DbTracker::deleteRows('rangetype', 'idrangetype', $id);
	}

	public function getRangeTypeView ($id)
	{
		$sql = "SELECT idrangetype as id
					FROM rangetype
					WHERE idrangetype=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data = Array(
				'language' => $this->getRangeTypeTranslation($id),
				'rangetypecategorys' => $this->RangeTypeCategoryIds($id)
			);
		}
		return $Data;
	}

	public function getRangeTypeTranslation ($id)
	{
		$sql = "SELECT name, languageid
					FROM rangetypetranslation
					WHERE rangetypeid = :id";
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

	public function RangeTypeCategory ($id)
	{
		$sql = 'SELECT categoryid as id
				FROM rangetypecategory
				WHERE rangetypeid=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function RangeTypeCategoryIds ($id)
	{
		$Data = $this->RangeTypeCategory($id);
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[] = $key['id'];
		}
		return $tmp;
	}

	public function addNewRangeType ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newRangeTypeId = $this->addRangeType($Data);
			$this->addRangeTypeCategory($Data['category'], $newRangeTypeId);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_NEW_RANGETYPE_ADD'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function addRangeTypeCategory ($array, $RangeTypeId)
	{
		foreach ($array as $key => $value){
			$sql = 'INSERT INTO rangetypecategory (rangetypeid, categoryid)
						VALUES (:rangetypeid, :categoryid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('rangetypeid', $RangeTypeId);
			$stmt->bindValue('categoryid', $value);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function addRangeType ($Data)
	{
		$sql = 'INSERT INTO rangetype (adddate) VALUES (NOW())';
		$stmt = Db::getInstance()->prepare($sql);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_NEW_RANGE_TYPE_ADD'), 15, $e->getMessage());
		}
		
		$rangetypeid = Db::getInstance()->lastInsertId();
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO rangetypetranslation SET
						rangetypeid = :rangetypeid,
						name = :name, 
						languageid = :languageid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('rangetypeid', $rangetypeid);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_RANGETYPE_TRANSLATION_EDIT'), 4, $e->getMessage());
			}
		}
		
		return $rangetypeid;
	}

	public function editRangeType ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->editRangeTypeName($Data, $id);
			$this->editRangeTypCategory($Data['category'], $id);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_RANGETYPE_EDIT'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function editRangeTypeName ($Data, $id)
	{
		DbTracker::deleteRows('rangetypetranslation', 'rangetypeid', $id);
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO rangetypetranslation SET
						rangetypeid = :rangetypeid,
						name = :name, 
						languageid = :languageid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('rangetypeid', $id);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_RANGETYPE_TRANSLATION_EDIT'), 4, $e->getMessage());
			}
		}
	
	}

	public function editRangeTypCategory ($array, $id)
	{
		DbTracker::deleteRows('rangetypecategory', 'rangetypeid', $id);
		
		foreach ($array as $key => $value){
			$sql = 'INSERT INTO rangetypecategory (rangetypeid, categoryid)
						VALUES (:rangetypeid, :categoryid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('rangetypeid', $id);
			$stmt->bindValue('categoryid', $value);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}
}