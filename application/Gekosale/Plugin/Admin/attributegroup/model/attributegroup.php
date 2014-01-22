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
 * $Id: attributegroup.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Plugin;

use Exception;

class AttributeGroupModel extends Component\Model
{

	public function getAttributesForGroup ($groupId)
	{
		$attributes = App::getModel('attributeproduct/attributeproduct')->getAttributeProductNamesByIds($groupId);
		foreach ($attributes as &$attribute){
			$attribute['values'] = App::getModel('attributeproduct/attributeproduct')->getAttributeProductValuesByAttributeGroupId($attribute['id']);
		}
		return $attributes;
	}

	public function getGroupsForCategory ($categoryIds)
	{
		
		$Data = Array();
		if (! isset($categoryIds) || ! is_array($categoryIds) || ! count($categoryIds)){
			$categoryIds = Array(
				0
			);
		}
		$inArray = Array();
		foreach ($categoryIds as $i => $categoryId){
			$inArray[] = ':categoryId' . $i;
		}
		$sql = 'SELECT DISTINCT
						AG.attributegroupnameid AS id,
						AGN.name AS name,
						categoryid IN (' . implode(', ', $inArray) . ') AS current_category
					FROM
						attributegroup AG
						LEFT JOIN categoryattributeproduct CAP ON CAP.attributeproductid = AG.attributeproductid
						LEFT JOIN attributegroupname AGN ON AGN.idattributegroupname = AG.attributegroupnameid
					GROUP BY id
					ORDER BY current_category DESC';
		$stmt = Db::getInstance()->prepare($sql);
		foreach ($categoryIds as $i => $categoryId){
			$stmt->bindValue('categoryId' . $i, $categoryId);
		}
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $rs['name'],
				'current_category' => ($rs['current_category'] ? true : false)
			);
		}
		return $Data;
	}

	public function getSugestVariant ($id)
	{
		$sql = 'SELECT 
					attributegroupnameid AS sets
				FROM productattributeset 
				WHERE productid=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'sets' => $rs['sets']
			);
		}
		return $Data;
	}

	public function getGroup ($idattributegroupname)
	{
		$sql = 'SELECT idattributegroupname AS id, name
				FROM attributegroupname 
				WHERE idattributegroupname=:idattributegroupname';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idattributegroupname', $idattributegroupname);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'id' => $rs['id'],
				'name' => $rs['name'],
				'attributes' => $this->getAllAttributeGroup($idattributegroupname)
			);
			$Data['category'] = $this->getAllCategoryAttributeProduct($Data['attributes']);
		}
		return $Data;
	}

	public function getAllAttributeGroup ($idattributegroupname)
	{
		$sql = 'SELECT attributeproductid
					FROM attributegroup 
					WHERE attributegroupnameid=:idattributegroupname';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idattributegroupname', $idattributegroupname);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['attributeproductid'];
		}
		return $Data;
	}

	public function getAllAttributeGroupToSelect ($id)
	{
		$sql = 'SELECT attributeproductid
					FROM attributegroup
					WHERE attributegroupnameid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['attributeproductid'];
		}
		return $Data;
	}

	public function getAllCategoryAttributeProduct ($attributes)
	{
		$Data = Array();
		if (count($attributes) > 0){
			$sql = 'SELECT DISTINCT categoryid
					FROM categoryattributeproduct 
					WHERE attributeproductid IN(' . implode(',', $attributes) . ')';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[] = $rs['categoryid'];
			}
		}
		return $Data;
	}

	public function getAllAttributeGroupName ()
	{
		$sql = 'SELECT idattributegroupname as id, name FROM attributegroupname';
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

	public function addEmptyGroup ($request)
	{
		if (! isset($request['name']) || ! strlen($request['name'])){
			$autoNameBase = $this->trans('TXT_NEW_ATTRIBUTE_GROUP');
			$sql = "SELECT name FROM attributegroupname WHERE name LIKE :pattern";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('pattern', $autoNameBase . '%');
			$stmt->execute();
			$existingNames = Array();
			while ($rs = $stmt->fetch()){
				$existingNames[] = $rs['name'];
			}
			$i = 1;
			do{
				$nameAlreadyExists = false;
				$autoName = $autoNameBase . (($i > 1) ? ' ' . $i : '');
				foreach ($existingNames as $name){
					if ($name == $autoName){
						$nameAlreadyExists = true;
						break;
					}
				}
				$i ++;
			}
			while ($nameAlreadyExists);
			$request['name'] = $autoName;
		}

		$sql = "SELECT COUNT(idattributegroupname) AS total FROM attributegroupname WHERE name = :pattern";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('pattern', $request['name']);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs['total'] == 0){
			$sql = 'INSERT INTO attributegroupname(name) VALUES (:name)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $request['name']);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				echo 1;
				throw new Exception($e->getMessage());
			}
			return Array(
				'id' => Db::getInstance()->lastInsertId()
			);
		}
		else{
			return Array(
				'error' => $this->trans('ERR_ATTRIBUTE_PRODUCT_GROUP_ALREADY_EXISTS')
			);
		}
	
	}

	public function deleteGroup ($id)
	{
		$sql = "
			SELECT
				OPA.idorderproductattribute
			FROM
				orderproductattribute OPA
			WHERE
				OPA.attributeproductvalueid IN
				(

					SELECT
						idattributeproductvalue
					FROM
						attributegroup AG
					INNER JOIN
						attributeproduct AP ON AP.idattributeproduct = AG.attributeproductid
					INNER JOIN
						attributeproductvalue APV ON APV.attributeproductid = AP.idattributeproduct
					WHERE
						AG.attributegroupnameid = 8
				)";

		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('attributegroupnameid', $id['id']);
		$stmt->execute();

		$rs = $stmt->fetch();

		if ( !empty($rs)) {
			App::getContainer()->get('session')->setVolatileErrorMessage($this->trans('ERR_BIND_ATTRIBUTE_VALUE'));
			return FALSE;
		}

		DbTracker::deleteRows('attributegroupname', 'idattributegroupname', $id);
	}

	public function editAttributeGroup ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			if ( !$this->deleteValue($Data['attributes']['editor'])) {
				return false;
			}

			$this->updateAttributeGroupName($Data, $id);
			$this->UpdateAttributeGroup($Data, $id);
			$allAttribute = $this->getAllAttributeGroupToSelect($id);
			if (is_array($Data['category'])){
				$this->updateCategoryAttributeProduct($Data, $allAttribute, $id);
			}
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_ATTRIBUTES_EDIT'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}
	
	public function editMigrationAttributeGroup ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->UpdateAttributeMigrationGroup($Data, $id);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_ATTRIBUTES_EDIT'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}	
	
	public function UpdateAttributeMigrationGroup ($Data, $attributegroupnameid)
	{
		if (isset($Data['attributes'])){
			foreach ($Data['attributes']['editor'] as $key => $attributeproductid){
				$checkid = substr($attributeproductid['id'], 0, 3);

				// Dodawanie nowego zestwu cech + warianty
				$attributeproductids = $this->addNewAttributeProduct($attributeproductid['name']);
				$sql = 'INSERT INTO attributegroup(attributegroupnameid, attributeproductid) 
							VALUES (:attributegroupnameid, :attributeproductid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('attributegroupnameid', $attributegroupnameid);
				$stmt->bindValue('attributeproductid', $attributeproductids);

				try{
					$stmt->execute();
					foreach ($attributeproductid['values'] as $key => $valueid){
						$sql = 'INSERT INTO attributeproductvalue(name, attributeproductid) VALUES (:name, :attributeproductid)';
						$stmt = Db::getInstance()->prepare($sql);
						$stmt->bindValue('name', $valueid['name']);
						$stmt->bindValue('attributeproductid', $attributeproductids);

						try{
							$stmt->execute();
						}
						catch (Exception $e){
							throw new Exception($e->getMessage());
						}
					}
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}
	}	
	

	public function deleteValue ($Data)
	{
		$recordsNotDeleted = array();
		foreach ($Data as $parent){
			$sql = 'SELECT attributeproductid, idattributeproductvalue 
						FROM attributeproductvalue 
						WHERE attributeproductid=:atrid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('atrid', $parent['id']);
			$stmt->execute();
			$Data = Array();
			while ($rs = $stmt->fetch()){
				$idattr = $rs['idattributeproductvalue'];
				$Data[$idattr] = $rs['idattributeproductvalue'];
			}
			foreach ($parent['values'] as $children){
				if (in_array($children['id'], $Data)){
					unset($Data[$children['id']]);
				}
			}
			foreach ($Data as $value){
				$sql = "SELECT idorderproductattribute FROM orderproductattribute WHERE attributeproductvalueid = :attrvalueid LIMIT 1";
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('attrvalueid', $value);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
				$rs = $stmt->fetch();
				if (!empty($rs)) {
					$recordsNotDeleted[] = $productid;
					continue;
				}

				DbTracker::deleteRows('productattributevalueset', 'attributeproductvalueid', $value);
				
				$sqlDelete = 'DELETE FROM attributeproductvalue WHERE attributeproductid=:atrid AND idattributeproductvalue=:attrvalueid ';
				$stmt = Db::getInstance()->prepare($sqlDelete);
				$stmt->bindValue('atrid', $parent['id']);
				$stmt->bindValue('attrvalueid', $value);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}

		App::getContainer()->get('session')->setVolatileErrorMessage($this->trans('ERR_BIND_ATTRIBUTE_VALUE'));

		return (count($recordsNotDeleted) === 0);
	}

	public function addAttributeProductValues ($value, $productattrid)
	{
		foreach ($value as $key){
			$sql = 'INSERT INTO attributeproductvalue(name, attributeproductid) 
						VALUES (:name, :productattrid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $key);
			$stmt->bindValue('productattrid', $productattrid);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function updateAttributeGroupName ($Data, $id)
	{
		$sql = 'UPDATE attributegroupname SET 
					name=:name
				WHERE idattributegroupname = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('name', $Data['attributegroupname']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($this->trans('ERR_ATTRIBUTE_GROUP_UPDATE'), 1, $e->getMessage());
			return false;
		}
		return true;
	}

	public function RenameAttribute ($attributeId, $newName)
	{
		$sql = 'UPDATE attributeproduct SET name = :name WHERE idattributeproduct = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $attributeId);
		$stmt->bindValue('name', $newName);
		$stmt->execute();
	}

	public function RenameValue ($attributeId, $newName)
	{
		$sql = 'UPDATE attributeproductvalue SET name = :name WHERE idattributeproductvalue = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $attributeId);
		$stmt->bindValue('name', $newName);
		$stmt->execute();
	}

	public function DeleteAttribute ($id)
	{
		$sql = "
			SELECT
				OPA.idorderproductattribute
			FROM
				orderproductattribute OPA
			WHERE
				OPA.attributeproductvalueid IN
				(
					SELECT
						APV.idattributeproductvalue
					FROM
						attributeproductvalue APV
					WHERE
						APV.attributeproductid = :attributeproductid
				)";

		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('attributeproductid', $id);
		$stmt->execute();

		$rs = $stmt->fetch();

		if ($rs) {
			throw new Exception($this->trans('ERR_BIND_ATTRIBUTE_VALUE'));
		}

		DbTracker::deleteRows('attributeproductvalue', 'attributeproductid', $id);
		
		DbTracker::deleteRows('attributeproduct', 'idattributeproduct', $id);
	}

	public function RemoveAttributeFromGroup ($attributeId, $groupId)
	{
		$sql = "
			SELECT
				OPA.idorderproductattribute
			FROM
				orderproductattribute OPA
			WHERE
				OPA.attributeproductvalueid IN
				(
					SELECT
						APV.idattributeproductvalue
					FROM
						attributeproductvalue APV
					WHERE
						APV.attributeproductid = :attributeproductid
				)";

		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('attributeproductid', $attributeId);
		$stmt->execute();

		$rs = $stmt->fetch();

		if ($rs) {
			return;
		}

		$sql = 'DELETE FROM attributegroup WHERE attributegroupnameid = :attributegroupnameid AND attributeproductid = :attributeproductid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('attributegroupnameid', $groupId);
		$stmt->bindValue('attributeproductid', $attributeId);
		$stmt->execute();
	}

	public function UpdateAttributeGroup ($Data, $attributegroupnameid)
	{
		DbTracker::deleteRows('attributegroup', 'attributegroupnameid', $attributegroupnameid);
		
		if (isset($Data['attributes'])){
			foreach ($Data['attributes']['editor'] as $key => $attributeproductid){
				$checkid = substr($attributeproductid['id'], 0, 3);
				if ($checkid != 'new'){
					// Dodawanie istniejacego zestwu cech + warianty
					$sql = 'INSERT INTO attributegroup(attributegroupnameid, attributeproductid) 
								VALUES (:attributegroupnameid, :attributeproductid)';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('attributegroupnameid', $attributegroupnameid);
					$stmt->bindValue('attributeproductid', $attributeproductid['id']);
					
					try{
						$stmt->execute();
						foreach ($attributeproductid['values'] as $key => $valueid){
							$checknewid = substr($valueid['id'], 0, 3);
							if ($checknewid == 'new'){
								$sql = 'INSERT INTO attributeproductvalue(name, attributeproductid) 
											VALUES (:name, :attributeproductid)';
								$stmt = Db::getInstance()->prepare($sql);
								$stmt->bindValue('name', $valueid['name']);
								$stmt->bindValue('attributeproductid', $attributeproductid['id']);
								
								try{
									$stmt->execute();
								}
								catch (Exception $e){
									throw new Exception($e->getMessage());
								}
							}
						}
					}
					catch (Exception $e){
						throw new Exception($e->getMessage());
					}
				}
				else{
					// Dodawanie nowego zestwu cech + warianty
					$attributeproductids = $this->addNewAttributeProduct($attributeproductid['name']);
					$sql = 'INSERT INTO attributegroup(attributegroupnameid, attributeproductid) 
								VALUES (:attributegroupnameid, :attributeproductid)';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('attributegroupnameid', $attributegroupnameid);
					$stmt->bindValue('attributeproductid', $attributeproductids);
					
					try{
						$stmt->execute();
						foreach ($attributeproductid['values'] as $key => $valueid){
							$sql = 'INSERT INTO attributeproductvalue(name, attributeproductid) VALUES (:name, :attributeproductid)';
							$stmt = Db::getInstance()->prepare($sql);
							$stmt->bindValue('name', $valueid['name']);
							$stmt->bindValue('attributeproductid', $attributeproductids);
							
							try{
								$stmt->execute();
							}
							catch (Exception $e){
								throw new Exception($e->getMessage());
							}
						}
					}
					catch (Exception $e){
						throw new Exception($e->getMessage());
					}
				}
			}
		}
	}

	public function updateCategoryAttributeProduct ($Data, $attr, $id)
	{
		foreach ($attr as $attrid){
			if (! is_array($attrid)){
				$sqlDelete = 'DELETE FROM categoryattributeproduct WHERE attributeproductid=:attrid';
				$stmt = Db::getInstance()->prepare($sqlDelete);
				$stmt->bindValue('attrid', $attrid);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
				if (isset($Data['category'])){
					foreach ($Data['category'] as $key => $catid){
						$sql = 'INSERT INTO categoryattributeproduct(categoryid, attributeproductid, attributegroupnameid) VALUES (:categoryid, :attributeproductid, :attributegroupnameid)';
						$stmt = Db::getInstance()->prepare($sql);
						$stmt->bindValue('categoryid', $catid);
						$stmt->bindValue('attributeproductid', $attrid);
						$stmt->bindValue('attributegroupnameid', $id);
						
						try{
							$stmt->execute();
						}
						catch (Exception $e){
							throw new Exception($e->getMessage());
						}
					}
				}
			}
		}
	}

	public function addNewAttributeProduct ($attribute)
	{
		$sql = 'INSERT INTO attributeproduct(name) VALUES (:name)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $attribute);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addAttributeToGroup ($attributeId, $groupId)
	{
		$sql = 'INSERT INTO attributegroup(attributegroupnameid, attributeproductid)
					VALUES (:attributegroupnameid, :attributeproductid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('attributegroupnameid', $attributeId);
		$stmt->bindValue('attributeproductid', $groupId);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($this->trans('ERR_CATEGORY_ATTRIBUTEPRODUCT_ADD'));
		}
	}
}