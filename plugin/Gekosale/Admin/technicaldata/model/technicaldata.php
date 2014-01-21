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
 * $Id: technicaldata.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale;

use sfEvent;

class TechnicalDataModel extends Component\Model
{
	const FIELD_STRING = 1;
	const FIELD_MULTILINGUAL_STRING = 2;
	const FIELD_TEXT = 3;
	const FIELD_IMAGE = 4;
	const FIELD_BOOLEAN = 5;
	const FIELD_SELECT = 6;
	// const FIELD_MULTISELECT = 7;
	protected $languages;

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
		$this->languages = App::getModel('Language')->getLanguageALLToSelect();
	}

	protected function getLanguageColumnString ($table, $valueColumnName = 'name')
	{
		$languageColumnName = 'languageid';
		$columns = Array();
		foreach ($this->languages as $languageId => $languageName){
			$columns[] = "GROUP_CONCAT(DISTINCT IF({$table}translation.{$languageColumnName} = {$languageId}, {$table}translation.{$valueColumnName}, '') SEPARATOR '') AS `{$valueColumnName}_{$languageId}`";
		}
		return implode(", ", $columns);
	}

	public function GetSets ($productId, $categoryIds)
	{
		$sql = 'SELECT
					TS.idtechnicaldataset AS id,
					TS.name AS caption
				FROM
					technicaldataset TS
				ORDER BY
					TS.name ASC';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function GetSetData ($setId)
	{
		$sql = 'SELECT
					TG.idtechnicaldatagroup AS id,
					' . $this->getLanguageColumnString('technicaldatagroup') . '
				FROM
					technicaldatagroup TG
					LEFT JOIN technicaldatagrouptranslation ON technicaldatagrouptranslation.technicaldatagroupid = TG.idtechnicaldatagroup
					LEFT JOIN technicaldatasetgroup TSG ON TG.idtechnicaldatagroup = TSG.technicaldatagroupid
				WHERE
					TSG.technicaldatasetid = :setId
				GROUP BY
					TG.idtechnicaldatagroup
				ORDER BY
					TSG.order ASC';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('setId', $setId);
		$stmt->execute();
		$groups = Array();
		$groupIndices = Array();
		while ($rs = $stmt->fetch()){
			$groupIndices[] = $rs['id'];
			$captions = Array();
			foreach ($this->languages as $languageId => $languageName){
				$captions[$languageId] = $rs['name_' . $languageId];
			}
			$groups[] = Array(
				'id' => $rs['id'],
				'caption' => $captions,
				'children' => Array(),
				'set_id' => $setId
			);
		}
		if (count($groups)){
			$sql = 'SELECT
						TA.idtechnicaldataattribute AS id,
						TA.type AS type,
						TSG.technicaldatagroupid AS group_id,
						' . $this->getLanguageColumnString('technicaldataattribute', 'name') . ',
						' . $this->getLanguageColumnString('technicaldataattribute', 'defaults') . '
					FROM
						technicaldataattribute TA
						LEFT JOIN technicaldataattributetranslation ON technicaldataattributetranslation.technicaldataattributeid = TA.idtechnicaldataattribute
						LEFT JOIN technicaldatasetgroupattribute TGA ON TA.idtechnicaldataattribute = TGA.technicaldataattributeid
						LEFT JOIN technicaldatasetgroup TSG ON TGA.technicaldatasetgroupid = TSG.idtechnicaldatasetgroup
					WHERE
						TSG.technicaldatagroupid IN (' . implode(',', $groupIndices) . ')
						AND TSG.technicaldatasetid = :setId
					GROUP BY
						TA.idtechnicaldataattribute
					ORDER BY
						TSG.order ASC,
						TGA.order ASC';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('setId', $setId);
			$stmt->execute();
			$groupIndex = 0;
			while ($rs = $stmt->fetch()){
				$currentGroupIndex = $rs['group_id'];
				if ($currentGroupIndex != $groups[$groupIndex]['id']){
					if ($currentGroupIndex != $groups[++ $groupIndex]['id']){
						throw new CoreException('Something\'s wrong with the technical data indices...');
					}
				}
				$captions = Array();
				foreach ($this->languages as $languageId => $languageName){
					$captions[$languageId] = $rs['name_' . $languageId];
					$defaults[$languageId] = explode("\r\n", $rs['defaults_' . $languageId]);
				}
				$groups[$groupIndex]['children'][] = Array(
					'id' => $rs['id'],
					'type' => (int) $rs['type'],
					'caption' => $captions,
					'defaults' => $defaults,
					'set_id' => $setId
				);
			}
		}
		return $groups;
	}

	public function SaveSet ($setId, $setName, $setData)
	{
		if ($setId == 'new'){
			$sql = 'INSERT INTO technicaldataset SET name = :name';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $setName);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
			$setId = Db::getInstance()->lastInsertId();
		}
		else{
			$this->deleteSetData($setId);
		}
		foreach ($setData as $groupOrder => $group){
			if (substr($group['id'], 0, 3) == 'new'){
				$group['id'] = $this->SaveGroup('new', $group['caption']);
			}
			$sql = 'INSERT INTO	technicaldatasetgroup SET
						technicaldatasetid = :setId,
						technicaldatagroupid = :groupId,
						`order` = :order';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('setId', $setId);
			$stmt->bindValue('groupId', $group['id']);
			$stmt->bindValue('order', $groupOrder);
			$stmt->execute();
			$setGroupId = Db::getInstance()->lastInsertId();
			if (isset($group['children']) and is_array($group['children'])){
				foreach ($group['children'] as $attributeOrder => $attribute){
					if (substr($attribute['id'], 0, 3) == 'new'){
						$attribute['id'] = $this->SaveAttribute('new', $attribute['caption'], $attribute['type']);
					}
					$sql = 'INSERT INTO	technicaldatasetgroupattribute	SET
								technicaldatasetgroupid = :setGroupId,
								technicaldataattributeid = :attributeId,
								`order` = :order';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('setGroupId', $setGroupId);
					$stmt->bindValue('attributeId', $attribute['id']);
					$stmt->bindValue('order', $attributeOrder);
					try{
						$stmt->execute();
					}
					catch (Exception $e){
						throw new Exception($e->getMessage());
					}
				}
			}
		}
		return $setId;
	}

	protected function deleteSetData ($id)
	{
		DbTracker::deleteRows('technicaldatasetgroup', 'technicaldatasetid', $id);
	}

	public function GetGroups ($setId = 0)
	{
		$sql = 'SELECT
					TG.idtechnicaldatagroup AS id,
					' . $this->getLanguageColumnString('technicaldatagroup') . ',
					TDSG.technicaldatasetid AS set_id
				FROM
					technicaldatagroup TG
					LEFT JOIN technicaldatagrouptranslation ON technicaldatagrouptranslation.technicaldatagroupid = TG.idtechnicaldatagroup
					LEFT JOIN technicaldatasetgroup TDSG ON TDSG.technicaldatagroupid = TG.idtechnicaldatagroup
				GROUP BY
					TG.idtechnicaldatagroup';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$groups = Array();
		while ($rs = $stmt->fetch()){
			$captions = Array();
			foreach ($this->languages as $languageId => $languageName){
				$captions[$languageId] = $rs['name_' . $languageId];
			}
			$groups[] = Array(
				'id' => $rs['id'],
				'set_id' => (int) $rs['set_id'],
				'caption' => $captions,
				'attributes' => $this->GetAttributesByGroupId($rs['id'])
			);
		}
		return $groups;
	}

	public function SaveGroup ($groupId, $groupName)
	{
		if (substr($groupId, 0, 3) == 'new'){
			$sql = 'INSERT INTO technicaldatagroup SET adddate = NOW()';
			$stmt = Db::getInstance()->prepare($sql);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
			$groupId = Db::getInstance()->lastInsertId();
		}
		else{
			DbTracker::deleteRows('technicaldatagrouptranslation', 'technicaldatagroupid', $groupId);
		}
		foreach ($groupName as $languageId => $name){
			$sql = 'INSERT INTO	technicaldatagrouptranslation SET
						technicaldatagroupid = :groupId,
						languageid = :languageId,
						name = :name';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('groupId', $groupId);
			$stmt->bindValue('languageId', $languageId);
			$stmt->bindValue('name', $name);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
		return $groupId;
	}

	public function DeleteGroup ($id)
	{
		DbTracker::deleteRows('technicaldatagroup', 'idtechnicaldatagroup', $id);
	}

	public function DeleteSet ($id)
	{
		DbTracker::deleteRows('technicaldataset', 'idtechnicaldataset', $id);
	}

	public function GetAttributes ()
	{
		$sql = 'SELECT
					TA.idtechnicaldataattribute AS id,
					TA.type AS type,
					' . $this->getLanguageColumnString('technicaldataattribute') . '
				FROM
					technicaldataattribute TA
					LEFT JOIN technicaldataattributetranslation ON technicaldataattributetranslation.technicaldataattributeid = TA.idtechnicaldataattribute
				GROUP BY
					TA.idtechnicaldataattribute';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$attributes = Array();
		while ($rs = $stmt->fetch()){
			$captions = Array();
			foreach ($this->languages as $languageId => $languageName){
				$captions[$languageId] = $rs['name_' . $languageId];
			}
			$attributes[] = Array(
				'id' => $rs['id'],
				'caption' => $captions,
				'type' => (int) $rs['type']
			);
		}
		return $attributes;
	}

	public function GetAttributesByGroupId ($id)
	{
		$sql = 'SELECT
					TDGA.technicaldataattributeid AS id
				FROM
					technicaldatasetgroupattribute TDGA
				LEFT JOIN
					technicaldatasetgroup TDG ON TDG.idtechnicaldatasetgroup = TDGA.technicaldatasetgroupid
				WHERE 
					TDG.technicaldatagroupid = :id
				';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$attributes = Array();
		while ($rs = $stmt->fetch()){
			$attributes[] = $rs['id'];
		}
		return $attributes;
	}

	public function SaveAttribute ($attributeId, $attributeName, $attributeType)
	{
		if (substr($attributeId, 0, 3) == 'new'){
			$sql = 'INSERT INTO technicaldataattribute SET type = :type';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('type', $attributeType);
			$stmt->execute();
			$attributeId = Db::getInstance()->lastInsertId();
		}
		else{
			$sql = 'UPDATE technicaldataattribute SET type = :type WHERE idtechnicaldataattribute = :id';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $attributeId);
			$stmt->bindValue('type', $attributeType);
			$stmt->execute();
			DbTracker::deleteRows('technicaldataattributetranslation', 'technicaldataattributeid', $attributeId);
		}
		
		foreach ($attributeName as $languageId => $name){
			$sql = 'INSERT INTO	technicaldataattributetranslation SET
						technicaldataattributeid = :attributeId,
						languageid = :languageId,
						name = :name';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('attributeId', $attributeId);
			$stmt->bindValue('languageId', $languageId);
			$stmt->bindValue('name', $name);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
		return $attributeId;
	}

	public function UpdateAttribute ($attributeId, $attributeType, $attributeNames = Array())
	{
		if (substr($attributeId, 0, 3) == 'new'){
			$sql = 'INSERT INTO technicaldataattribute SET type = :type';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('type', $attributeType);
			$stmt->execute();
			$attributeId = Db::getInstance()->lastInsertId();
			
			foreach ($attributeNames as $languageId => $attributeName){
				$sql = 'INSERT INTO	technicaldataattributetranslation SET
						technicaldataattributeid = :attributeId,
						languageid = :languageId,
						name = :name';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('attributeId', $attributeId);
				$stmt->bindValue('languageId', $languageId);
				$stmt->bindValue('name', $attributeName);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}
		else{
			$sql = 'UPDATE technicaldataattribute SET type = :type WHERE idtechnicaldataattribute = :id';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $attributeId);
			$stmt->bindValue('type', $attributeType);
			$stmt->execute();
		}
		
		return $attributeId;
	}

	public function DeleteAttribute ($id)
	{
		DbTracker::deleteRows('technicaldataattribute', 'idtechnicaldataattribute', $id);
	}

	public function GetValuesForProduct ($productId, $setId)
	{
		$sql = 'SELECT
					TG.idtechnicaldatagroup AS id,
					TSG2.technicaldatasetid AS set_id,
					' . $this->getLanguageColumnString('technicaldatagroup') . '
				FROM
					technicaldatagroup TG
					LEFT JOIN technicaldatagrouptranslation ON technicaldatagrouptranslation.technicaldatagroupid = TG.idtechnicaldatagroup
					LEFT JOIN producttechnicaldatagroup TSG ON TG.idtechnicaldatagroup = TSG.technicaldatagroupid
					LEFT JOIN technicaldatasetgroup TSG2 ON TG.idtechnicaldatagroup = TSG2.technicaldatagroupid AND TSG2.technicaldatasetid = :setid
				WHERE
					TSG.productid = :productId
				GROUP BY
					TG.idtechnicaldatagroup
				ORDER BY
					TSG.order ASC';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productId', $productId);
		$stmt->bindValue('setid', $setId);
		$stmt->execute();
		$groups = Array();
		$groupIndices = Array();
		while ($rs = $stmt->fetch()){
			$groupIndices[] = $rs['id'];
			$captions = Array();
			foreach ($this->languages as $languageId => $languageName){
				$captions[$languageId] = $rs['name_' . $languageId];
			}
			
			$children = $this->getChildrenForProduct($productId, $rs['id'], $setId);
			
			if (! empty($children)){
				$groups[] = Array(
					'id' => $rs['id'],
					'set_id' => $rs['set_id'],
					'caption' => $captions,
					'children' => $children
				);
			}
		}
		
		return $groups;
	}

	public function getChildrenForProduct ($productId, $attributeGroupId, $setId)
	{
		$sql = 'SELECT
					TA.idtechnicaldataattribute AS id,
					TA.type AS type,
					TGA.value AS value,
					TSG.technicaldatagroupid AS group_id,
					' . $this->getLanguageColumnString('technicaldataattribute', 'name') . ',
					' . $this->getLanguageColumnString('technicaldataattribute', 'defaults') . ',
					' . $this->getLanguageColumnString('producttechnicaldatagroupattribute', 'value') . '
				FROM
					technicaldataattribute TA
					LEFT JOIN technicaldataattributetranslation ON technicaldataattributetranslation.technicaldataattributeid = TA.idtechnicaldataattribute
					LEFT JOIN producttechnicaldatagroupattribute TGA ON TA.idtechnicaldataattribute = TGA.technicaldataattributeid
					LEFT JOIN producttechnicaldatagroupattributetranslation ON producttechnicaldatagroupattributetranslation.producttechnicaldatagroupattributeid = TGA.idproducttechnicaldatagroupattribute
					LEFT JOIN producttechnicaldatagroup TSG ON TGA.producttechnicaldatagroupid = TSG.idproducttechnicaldatagroup
				WHERE
					TSG.productid = :productId AND TSG.technicaldatagroupid = :groupid
				GROUP BY
					TA.idtechnicaldataattribute,
					TGA.idproducttechnicaldatagroupattribute
				ORDER BY
					TSG.order ASC,
					TGA.order ASC';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productId', $productId);
		$stmt->bindValue('groupid', $attributeGroupId);
		$stmt->execute();
		$groupIndex = 0;
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$captions = Array();
			$defaults = Array();
			foreach ($this->languages as $languageId => $languageName){
				$captions[$languageId] = $rs['name_' . $languageId];
				$defaults[$languageId] = $rs['defaults_' . $languageId];
			}
			$type = $rs['type'];
			switch ($type) {
				case self::FIELD_MULTILINGUAL_STRING:
					$value = Array();
					foreach ($this->languages as $languageId => $languageName){
						$value[$languageId] = $rs['value_' . $languageId];
					}
					break;
				default:
					$value = $rs['value'];
			}
			$Data[] = Array(
				'id' => $rs['id'],
				'set_id' => $setId,
				'type' => (int) $type,
				'value' => $value,
				'caption' => $captions,
				'defaults' => $defaults
			);
		}
		return $Data;
	}

	public function SaveValuesForProduct ($productId, $productData)
	{
		try{
			$this->DeleteValuesForProduct($productId);
			if (! isset($productData['groups']) or ! is_array($productData['groups'])){
				return;
			}
			foreach ($productData['groups'] as $groupOrder => $group){
				if (substr($group['id'], 0, 3) == 'new'){
					$group['id'] = $this->SaveGroup('new', $group['caption']);
				}
				$sql = 'INSERT INTO producttechnicaldatagroup SET
							productid = :productId,
							technicaldatagroupid = :groupId,
							`order` = :order';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('productId', $productId);
				$stmt->bindValue('groupId', $group['id']);
				$stmt->bindValue('order', $groupOrder);
				$stmt->execute();
				$productGroupId = Db::getInstance()->lastInsertId();
				if (isset($group['attributes']) and is_array($group['attributes'])){
					foreach ($group['attributes'] as $attributeOrder => $attribute){
						if (substr($attribute['id'], 0, 3) == 'new'){
							$attribute['id'] = $this->SaveAttribute('new', $attribute['caption'], $attribute['type']);
						}
						$sql = 'INSERT INTO	producttechnicaldatagroupattribute SET
									producttechnicaldatagroupid = :productGroupId,
									technicaldataattributeid = :attributeId,
									`order` = :order,
									value = :value';
						$stmt = Db::getInstance()->prepare($sql);
						$stmt->bindValue('productGroupId', $productGroupId);
						$stmt->bindValue('attributeId', $attribute['id']);
						$stmt->bindValue('order', $attributeOrder);
						switch ($attribute['type']) {
							case self::FIELD_STRING:
								$stmt->bindValue('value', $attribute['value']);
								break;
							case self::FIELD_TEXT:
								$stmt->bindValue('value', $attribute['value']);
								break;
							case self::FIELD_BOOLEAN:
								$stmt->bindValue('value', $attribute['value'] ? '1' : '0');
								break;
							default:
								$stmt->bindValue('value', '');
						}
						$stmt->execute();
						$productGroupAttributeId = Db::getInstance()->lastInsertId();
						switch ($attribute['type']) {
							case self::FIELD_MULTILINGUAL_STRING:
								if (! is_array($attribute['value'])){
									break;
								}
								foreach ($attribute['value'] as $languageId => $value){
									$sql = 'INSERT INTO	producttechnicaldatagroupattributetranslation SET
												producttechnicaldatagroupattributeid = :productGroupAttributeId,
												languageid = :languageId,
												value = :value';
									$stmt = Db::getInstance()->prepare($sql);
									$stmt->bindValue('productGroupAttributeId', $productGroupAttributeId);
									$stmt->bindValue('languageId', $languageId);
									$stmt->bindValue('value', $value);
									$stmt->execute();
								}
								break;
						}
					}
				}
			}
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCT_TECHNICAL_DATA_ADD'), 112, $e->getMessage());
		}
	}

	public function DeleteValuesForProduct ($id)
	{
		DbTracker::deleteRows('producttechnicaldatagroup', 'productid', $id);
	}

	public function getAllTechnicalDataName ()
	{
		$sql = 'SELECT idtechnicaldataset as id, name FROM technicaldataset';
		$stmt = Db::getInstance()->prepare($sql);
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

	public function getGroup ($idtechnicaldataset)
	{
		$sql = 'SELECT 
					idtechnicaldataset AS id, 
					name
				FROM technicaldataset
				WHERE idtechnicaldataset = :idtechnicaldataset';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idtechnicaldataset', $idtechnicaldataset);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'id' => $rs['id'],
				'name' => $rs['name'],
				'attributes' => $this->getAllTechnicalDataSetGroup($idtechnicaldataset)
			);
		}
		return $Data;
	}

	public function getAllTechnicalDataSetGroup ($idtechnicaldataset)
	{
		$sql = 'SELECT 
					technicaldatagroupid
				FROM technicaldatasetgroup
				WHERE technicaldatasetid = :technicaldatasetid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('technicaldatasetid', $idtechnicaldataset);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['technicaldatagroupid'];
		}
		return $Data;
	}

	public function getTechnicalDataFull ()
	{
		$sql = 'SELECT
					technicaldatagroupid,
					' . $this->getLanguageColumnString('technicaldatagroup', 'name') . '
				FROM technicaldatagrouptranslation
				GROUP BY technicaldatagroupid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$captions = Array();
			foreach ($this->languages as $languageId => $languageName){
				$captions[$languageId] = $rs['name_' . $languageId];
			}
			$Data[] = Array(
				'id' => $rs['technicaldatagroupid'],
				'name' => $captions,
				'values' => $this->getTechnicalDataGroupValues($rs['technicaldatagroupid'])
			);
		}
		return $Data;
	}

	public function getTechnicalDataGroupValues ($id)
	{
		$sql = 'SELECT 
					TDA.idtechnicaldataattribute AS id, 
					TDA.type AS type,
					' . $this->getLanguageColumnString('technicaldataattribute', 'name') . ',
					' . $this->getLanguageColumnString('technicaldataattribute', 'defaults') . '
				FROM technicaldataattribute TDA
				LEFT JOIN technicaldataattributetranslation ON technicaldataattributetranslation.technicaldataattributeid = TDA.idtechnicaldataattribute
				LEFT JOIN technicaldatasetgroupattribute TDSG ON TDA.idtechnicaldataattribute = TDSG.technicaldataattributeid
				LEFT JOIN technicaldatasetgroup TDG ON TDG.idtechnicaldatasetgroup = TDSG.technicaldatasetgroupid
				WHERE TDG.technicaldatagroupid = :id AND TDG.technicaldatasetid = :technicaldatasetid
				GROUP BY TDA.idtechnicaldataattribute
				ORDER BY TDSG.order ASC';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('technicaldatasetid', $this->registry->core->getParam());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$name = Array();
			$defaults = Array();
			foreach ($this->languages as $languageId => $languageName){
				$name[$languageId] = $rs['name_' . $languageId];
				$defaults[$languageId] = $rs['defaults_' . $languageId];
			}
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $name,
				'defaults' => $defaults,
				'type' => $rs['type']
			);
		}
		
		return $Data;
	}

	public function addEmptyGroup ($request)
	{
		if (! isset($request['name']) || ! strlen($request['name'])){
			return Array(
				'error' => 'Nie podano nazwy grupy.'
			);
		}
		$sql = 'INSERT INTO technicaldataset SET name = :name';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $request['name']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		
		return Array(
			'id' => Db::getInstance()->lastInsertId()
		);
	}

	public function RenameAttribute ($attributeId, $newName, $languageId)
	{
		$sql = 'INSERT INTO technicaldatagrouptranslation SET 
					technicaldatagroupid = :id,
					languageid = :languageid,
					name = :name 
				ON DUPLICATE KEY UPDATE 
					name = :name ';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $attributeId);
		$stmt->bindValue('name', $newName);
		$stmt->bindValue('languageid', $languageId);
		$stmt->execute();
	}

	public function RenameValue ($attributeId, $newName, $languageId)
	{
		$sql = 'INSERT INTO technicaldataattributetranslation SET
					technicaldataattributeid = :id,
					languageid = :languageid,
					name = :name
				ON DUPLICATE KEY UPDATE
					name = :name ';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $attributeId);
		$stmt->bindValue('name', $newName);
		$stmt->bindValue('languageid', $languageId);
		$stmt->execute();
	}

	public function DeleteDataGroup ($attributeId, $setId)
	{
		DbTracker::deleteRows('technicaldatagroup', 'idtechnicaldatagroup', $attributeId);
	}

	public function editTechnicalData ($Data, $id)
	{
		if (! isset($Data['attributes']['editor'])){
			$sql = 'DELETE FROM technicaldatasetgroup WHERE technicaldatasetid = :technicaldatasetid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('technicaldatasetid', $id);
			$stmt->execute();
			return true;
		}
		
		Db::getInstance()->beginTransaction();
		
		$sql = 'UPDATE technicaldataset SET name = :name WHERE idtechnicaldataset = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('name', $Data['attributegroupname']);
		$stmt->execute();
		
		$existingGroups = Array();
		foreach ($Data['attributes']['editor'] as $order => $group){
			$checkid = substr($group['id'], 0, 3);
			if ($checkid != 'new'){
				$groupId = $group['id'];
			}
			else{
				$sql = 'INSERT INTO technicaldatagroup SET adddate = NOW()';
				$stmt = Db::getInstance()->prepare($sql);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
				
				$groupId = Db::getInstance()->lastInsertId();
				
				$sql = 'INSERT INTO	technicaldatagrouptranslation SET
							technicaldatagroupid = :groupId,
							languageid = :languageId,
							name = :name';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('groupId', $groupId);
				$stmt->bindValue('languageId', Helper::getLanguageId());
				$stmt->bindValue('name', $group['name']);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
			
			$existingGroups[] = $groupId;
			
			$sql = 'SELECT idtechnicaldatasetgroup FROM technicaldatasetgroup WHERE technicaldatasetid = :technicaldatasetid AND technicaldatagroupid = :technicaldatagroupid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('technicaldatasetid', $id);
			$stmt->bindValue('technicaldatagroupid', $groupId);
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$setGroupId = $rs['idtechnicaldatasetgroup'];
			}
			else{
				$sql = 'INSERT INTO	technicaldatasetgroup SET
							technicaldatasetid = :technicaldatasetid,
							technicaldatagroupid = :technicaldatagroupid,
							`order` = :order';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('technicaldatasetid', $id);
				$stmt->bindValue('technicaldatagroupid', $groupId);
				$stmt->bindValue('order', $order);
				$stmt->execute();
				$setGroupId = Db::getInstance()->lastInsertId();
			}
			
			$existingValues = Array();
			
			if (isset($group['values']) && ! empty($group['values'])){
				foreach ($group['values'] as $attributeOrder => $attribute){
					$attribute['id'] = $this->UpdateAttribute($attribute['id'], $attribute['type'], $attribute['name']);
					$existingValues[] = $attribute['id'];
					$sql = 'INSERT INTO	technicaldatasetgroupattribute	SET
								technicaldatasetgroupid = :setGroupId,
								technicaldataattributeid = :attributeId,
								`order` = :order';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('setGroupId', $setGroupId);
					$stmt->bindValue('attributeId', $attribute['id']);
					$stmt->bindValue('order', $attributeOrder);
					$stmt->execute();
					
					/*
					 * Refresh zestawu w produktach
					 */
					
					$this->refreshProductAttribute($attribute['id'], $groupId, $attributeOrder);
				}
			}
			
			if (! empty($existingValues)){
				$sql = 'DELETE FROM technicaldatasetgroupattribute WHERE technicaldatasetgroupid = :technicaldatasetgroupid AND technicaldataattributeid NOT IN (' . implode(',', $existingValues) . ')';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('technicaldatasetgroupid', $setGroupId);
				$stmt->execute();
			}
			else{
				$sql = 'DELETE FROM technicaldatasetgroupattribute WHERE technicaldatasetgroupid = :technicaldatasetgroupid';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('technicaldatasetgroupid', $setGroupId);
				$stmt->execute();
			}
		}
		
		$sql = 'DELETE FROM technicaldatasetgroup WHERE technicaldatasetid = :technicaldatasetid AND technicaldatagroupid NOT IN (' . implode(',', $existingGroups) . ')';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('technicaldatasetid', $id);
		$stmt->execute();
		Db::getInstance()->commit();
	}

	public function refreshProductAttribute ($technicaldataattributeid, $technicaldatagroupid, $attributeOrder)
	{
		$sql = 'SELECT idproducttechnicaldatagroup, productid FROM producttechnicaldatagroup WHERE technicaldatagroupid = :technicaldatagroupid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('technicaldatagroupid', $technicaldatagroupid);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$sql2 = 'SELECT idproducttechnicaldatagroupattribute FROM producttechnicaldatagroupattribute WHERE producttechnicaldatagroupid = :idproducttechnicaldatagroup AND technicaldataattributeid = :technicaldataattributeid';
			$stmt2 = Db::getInstance()->prepare($sql2);
			$stmt2->bindValue('idproducttechnicaldatagroup', $rs['idproducttechnicaldatagroup']);
			$stmt2->bindValue('technicaldataattributeid', $technicaldataattributeid);
			$stmt2->execute();
			$rs2 = $stmt2->fetch();
			if (! $rs2){
				$sql3 = 'INSERT INTO	producttechnicaldatagroupattribute SET
							producttechnicaldatagroupid = :productGroupId,
							technicaldataattributeid = :attributeId,
							`order` = :order,
							value = :value';
				$stmt3 = Db::getInstance()->prepare($sql3);
				$stmt3->bindValue('productGroupId', $rs['idproducttechnicaldatagroup']);
				$stmt3->bindValue('attributeId', $technicaldataattributeid);
				$stmt3->bindValue('order', $attributeOrder);
				$stmt3->bindValue('value', '');
				$stmt3->execute();
			}
		}
	}
}