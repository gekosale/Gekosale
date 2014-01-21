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
 * $Id: attributeproduct.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;
use sfEvent;

class AttributeProductModel extends Component\Model\Datagrid
{
	
	protected $valuesMultiInput;

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
	}

	protected function initValuesMultiInput ($multiinput)
	{
		if ((int) $this->registry->core->getParam()){
			$sql = 'SELECT idattributeproductvalue AS id, name
						FROM attributeproductvalue 
						WHERE attributeproductid = :id';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', (int) $this->registry->core->getParam());
			$stmt->execute();
			$rs = $stmt->fetch();
			$Data = Array();
			while ($rs = $stmt->fetch()){
				$Data[] = Array(
					'id' => $rs['id'],
					'value' => $rs['name']
				);
			}
			$multiinput->setValues($Data);
		}
	}

	public function getValuesMultiInputConfiguration ()
	{
		if (($this->valuesMultiInput == NULL) || ! ($this->valuesMultiInput instanceof MultiinputModel)){
			$this->valuesMultiInput = App::getModel('multiinput/multiinput');
			$this->initValuesMultiInput($this->valuesMultiInput);
		}
		return $this->valuesMultiInput;
	}

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('attributeproduct', Array(
			'idattributeproduct' => Array(
				'source' => 'A.idattributeproduct'
			),
			'name' => Array(
				'source' => 'A.name',
				'prepareForAutosuggest' => true
			),
			'valuecount' => Array(
				'source' => 'COUNT(DISTINCT V.idattributeproductvalue)',
				'filter' => 'having'
			),
			'productcount' => Array(
				'source' => 'COUNT(DISTINCT P.productid)',
				'filter' => 'having'
			),
			'adddate' => Array(
				'source' => 'A.adddate'
			)
		));
		$datagrid->setFrom('
				`attributeproduct` A
				LEFT JOIN `attributeproductvalue` V ON A.idattributeproduct = V.attributeproductid
				LEFT JOIN (
					SELECT
						AP.idattributeproduct AS attributeproductid,
						PAS.productid AS productid
					FROM
						`productattributeset` PAS
						LEFT JOIN `productattributevalueset` PAVS ON PAVS.productattributesetid = PAS.idproductattributeset
						LEFT JOIN `attributeproductvalue` APV ON APV.idattributeproductvalue = PAVS.attributeproductvalueid
						RIGHT JOIN `attributeproduct` AP ON AP.idattributeproduct = APV.attributeproductid
				) P ON P.attributeproductid = A.idattributeproduct
			');
		$datagrid->setGroupBy('
				A.idattributeproduct
			');
	}

	public function getNameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
	}

	public function getAttributeProductsForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteAttributeProducts ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteAttributeProducts'
		), $this->getName());
	}

	public function deleteAttributeProducts ($id)
	{
		DbTracker::deleteRows('attributeproduct', 'idattributeproduct', $id);
	}

	public function addAttributeGroup ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$groupId = $this->addAttributeGroupName($Data['attributeproductgroupname']);
			$this->addAttributeGroupValues($Data['attributeproductvalues'], $groupId);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_WHILE_ATTRIBUTE_PRODUCT_GROUP_ADD'), 114, $e->getMessage());
		}
		
		Db::getInstance()->commit();
	}

	public function addAttributeGroupName ($groupName)
	{
		$sql = 'INSERT INTO attributeproduct(name) VALUES (:name)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $groupName);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addAttributeGroupValues ($value, $groupId)
	{
		foreach ($value as $key){
			$sql = 'INSERT INTO attributeproductvalue(name, attributeproductid) VALUES (:valuename, :productattrid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('valuename', $key);
			$stmt->bindValue('productattrid', $groupId);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function getAttributeProductNames ()
	{
		$sql = 'SELECT idattributeproduct AS id, name FROM attributeproduct ORDER BY name';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function getAttributeProductNamesByIds ($attributegroupnameid)
	{
		$sql = 'SELECT distinct AP.idattributeproduct as id, AP.name
    					FROM attributegroup AG
						LEFT JOIN attributeproduct AP ON AP.idattributeproduct = AG.attributeproductid
						WHERE attributegroupnameid=:attributegroupnameid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('attributegroupnameid', $attributegroupnameid);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function getAttributeProductValuesByAttributeGroupId ($id)
	{
		$sql = 'SELECT idattributeproductvalue AS id, name 
					FROM attributeproductvalue
					WHERE attributeproductid = :attrid ORDER BY name';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('attrid', $id);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function getAttributeProductFull ()
	{
		$attrGroup = $this->getAttributeProductNames();
		$Data = Array();
		foreach ($attrGroup as $key => $value){
			$attrGroup[$key]['values'] = $this->getAttributeProductValuesByAttributeGroupId($value['id']);
		}
		return $attrGroup;
	}

	public function getAttributeProductNamesToSelect ()
	{
		$attr = $this->getAttributeProduct();
		$Data = Array();
		foreach ($attr as $value){
			$Data[$value['id']] = $value['name'];
		}
		return $Data;
	}

	public function getAttributeProductName ($id)
	{
		$sql = 'SELECT idattributeproduct AS id, name as attributeproductname
					FROM attributeproduct 
					WHERE idattributeproduct=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'attributeproductname' => $rs['attributeproductname'],
				'attributes' => $this->getAttributeValues($id)
			); // 'category' => $this->getAttributeCategory($id)
		
		}
		else{
			throw new CoreException($this->trans('ERR_ATTRIBUTEGROUP_NO_EXIST'));
		}
		return $Data;
	}

	public function getAttributeCategory ($id)
	{
		$sql = 'SELECT categoryid 
					FROM categoryattributeproduct 
					WHERE attributeproductid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data['categoryid'][] = $rs['categoryid'];
		}
		return $Data;
	}

	public function getAttributeValues ($id)
	{
		$sql = 'SELECT idattributeproductvalue AS ids, name as attributesname, attributeproductid as id
					FROM attributeproductvalue 
					WHERE attributeproductid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['ids']] = $rs['attributesname'];
		}
		return $Data;
	}

	public function getAttributeName ($id)
	{
		$sql = 'SELECT attributeproductid AS id, name as attributesname
					FROM attributeproductvalue 
					WHERE attributeproductid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'attributesname' => $rs['attributesname']
			);
		}
		return $Data;
	}

	public function updateAttribute ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->UpdateAttributeProductName($Data, $id);
			$this->updateAttributeValueName($Data['attributeproductvalues'], $id);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_ATTRIBUTES_EDIT'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function UpdateAttributeProductName ($Data, $id)
	{
		$sql = 'UPDATE attributeproduct SET name=:name WHERE idattributeproduct = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('name', $Data['attributeproductname']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
			return false;
		}
		return true;
	}

	public function updateAttributeValueName ($array, $id)
	{
		$sql = "SELECT idattributeproductvalue
					FROM attributeproductvalue
					WHERE attributeproductid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			(int) $idattribut = $rs['idattributeproductvalue'];
			$Data[$idattribut] = $idattribut;
		}
		foreach ($array as $key => $oldid){
			if (is_int($key)){
				$sql = 'UPDATE attributeproductvalue SET 
							name=:name
						WHERE 
						idattributeproductvalue = :key';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('key', $key);
				$stmt->bindValue('name', $oldid);
				
				$stmt->execute();
				unset($Data[$key]);
			}
			else{
				$sql = 'INSERT INTO attributeproductvalue(name, attributeproductid) VALUES (:name, :productattrid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('name', $oldid);
				$stmt->bindValue('productattrid', $id);
				
				$stmt->execute();
			}
		}
		foreach ($Data as $delete){
			DbTracker::deleteRows('attributeproductvalue', 'idattributeproductvalue', $delete);
		}
	}

	public function getAttributeNameById ($id)
	{
		$sql = 'SELECT name FROM attributeproductvalue WHERE idattributeproductvalue = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			$rs->first();
			return $rs['name'];
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function getAttributeNamesDistinctByArrayId ($Data)
	{
		$sql = 'SELECT DISTINCT name FROM attributeproductvalue 
			WHERE idattributeproductvalue IN (:id)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->setInInt('id', $Data);
		$Attributes = Array();
		try{
			$stmt->execute();
		}
		catch (Execute $e){
			throw new Exception($e->getMessage());
		}
		while ($rs = $stmt->fetch()){
			$Attributes[] = $rs['name'];
		}
		return $Attributes;
	}

	public function updateAttributeGroupName ($Data, $id)
	{
		$sql = 'UPDATE attributegroupname SET name=:name WHERE idattributegroupname = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('name', $Data['name']);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_ATTRIBUTE_GROUP_UPDATE'), 1, $e->getMessage());
			return false;
		}
		return true;
	}

	public function editAttributeCategory ($Data, $id)
	{
		DbTracker::deleteRows('categoryattributeproduct', 'attributeproductid', $id);
		
		foreach ($Data as $key => $categoryid){
			$sql = 'INSERT INTO categoryattributeproduct(categoryid, attributeproductid) VALUES (:categoryid, :attributeproductid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('categoryid', $categoryid);
			$stmt->bindValue('attributeproductid', $id);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}
}