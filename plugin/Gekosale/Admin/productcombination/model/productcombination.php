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

class ProductCombinationModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('combination', Array(
			'idcombination' => Array(
				'source' => 'C.idcombination'
			),
			'products' => Array(
				'source' => 'GROUP_CONCAT(DISTINCT SUBSTRING(CONCAT(\'<br />\', CONCAT(PC.numberofitems, \' x \', PT.name)), 1))',
				'filter' => 'having'
			),
			'discount' => Array(
				'source' => 'C.value'
			)
		));
		$datagrid->setFrom('
			`combination` C
			LEFT JOIN `productcombination` PC ON PC.combinationid = C.idcombination
			LEFT JOIN producttranslation PT ON PC.productid = PT.productid AND PT.languageid = :languageid
		');
		$datagrid->setGroupBy('
			C.idcombination
		');
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getCombinationForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteProductCombination ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteProductCombination'
		), $this->getName());
	}

	public function deleteProductCombination ($id)
	{
		DbTracker::deleteRows('combination', 'idcombination', $id);
	}

	public function editCombination ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->updateCombinationProductDataGrid($_POST, $id);
			$sql = 'UPDATE combination SET 
						value=:value
					WHERE idcombination = :id';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $id);
			$stmt->bindValue('value', $Data['discount']);
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->registry->core->getMessage('ERR_COMBINATION_EDIT'), 10, $e->getMessage());
		}
		
		DbTracker::deleteRows('combinationview', 'combinationid', $id);
		$this->addCombinationView($Data['view'], $id);
		Db::getInstance()->commit();
		return true;
	}

	public function updateCombinationProductDataGrid ($array, $id)
	{
		DbTracker::deleteRows('productcombination', 'combinationid', $id);
		if (is_array($array['related_products']['products'])){
			foreach ($array['related_products']['products'] as $value){
				$sql = 'INSERT INTO productcombination (productid, combinationid, numberofitems, productattributesetid) 
						VALUES (:productid, :combinationid, :numberofitems, :productattributesetid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('productid', $value['id']);
				$stmt->bindValue('productattributesetid', ($value['variant'] == 0) ? NULL : $value['variant']);
				$stmt->bindValue('combinationid', $id);
				$stmt->bindValue('numberofitems', $value['quantity']);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}
	}

	public function addCombination ($Data)
	{
		$sql = 'INSERT INTO `combination` (value) VALUES (:value)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('value', $Data['discount']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->registry->core->getMessage('ERR_COMBINATION_ADD'), 11, $e->getMessage());
		}
		
		return Db::getInstance()->lastInsertId();
	}

	public function addNewCombination ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newCombinationid = $this->addCombination($Data);
			$this->addCombinationProduct($Data, $newCombinationid);
			$this->addCombinationView($Data['view'], $newCombinationid);
		}
		catch (Exception $e){
			throw new CoreException($this->registry->core->getMessage('ERR_COMBINATIONPRODUCT_ADD'), 11, $e->getMessage());
		}
		Db::getInstance()->commit();
		
		return true;
	}

	public function addCombinationProduct ($Data, $newCombinationid)
	{
		if (is_array($Data['products'])){
			foreach ($Data['products'] as $product){
				$sql = 'INSERT INTO productcombination (productid, combinationid, numberofitems, productattributesetid) 
						VALUES (:productid, :combinationid, :numberofitems, :productattributesetid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('productid', $product['id']);
				$stmt->bindValue('productattributesetid', ((int) $product['variant'] > 0) ? $product['variant'] : NULL);
				$stmt->bindValue('combinationid', $newCombinationid);
				$stmt->bindValue('numberofitems', $product['quantity']);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->registry->core->getMessage('ERR_PRODUCTCOMBINATION_ADD'), 11, $e->getMessage());
				}
			}
		}
	}

	public function getProductsDataGrid ($combinationId)
	{
		$sql = "SELECT 
					numberofitems AS quantity, 
					productattributesetid as variant, 
					productid as id
 				FROM productcombination
				WHERE combinationid =:combinationId";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('combinationId', $combinationId);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'quantity' => $rs['quantity'],
				'variant' => $rs['variant']
			);
		}
		return $Data;
	}

	public function getCombinationView ($id)
	{
		$sql = "SELECT 
					C.idcombination AS id, 
					C.value
				FROM combination C
				WHERE idcombination = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'value' => $rs['value'],
				'products' => $this->getProductsDataGrid($id),
				'view' => $this->getStoreViews($id)
			);
		}
		else{
			throw new CoreException($this->registry->core->getMessage('ERR_PRODUCTCOMBINATION_NO_EXIST'));
		}
		return $Data;
	}

	public function getProductCombinationView ($id)
	{
		$sql = "SELECT PC.productattributesetid, PC.idproductcombination AS id, PT.shortdescription, 
					PT.name AS productname, P.sellprice, PC.numberofitems
					FROM productcombination PC
            		LEFT JOIN product P ON P.idproduct = PC.productid
            		LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
					WHERE combinationid=:id group by productname";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('languageid', App::getContainer()->get('session')->getActiveLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'productname' => $rs['productname'],
				'shortdescription' => $rs['shortdescription'],
				'numberofitems' => $rs['numberofitems'],
				'sellprice' => $rs['sellprice'],
				'productattributes' => $this->getProductAttribute($rs->getInt('productattributesetid'))
			);
		}
		return $Data;
	}

	public function getProductAttribute ($combinationId)
	{
		$sql = "SELECT attributeproductvalueid as combinationid, name as attribute
					FROM productattributevalueset
					LEFT JOIN attributeproductvalue APVS ON APVS.idattributeproductvalue =attributeproductvalueid
					WHERE productattributesetid =:combinationid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('combinationid', $combinationId);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'attribute' => $rs['attribute']
			);
		}
		return $Data;
	}

	public function getStoreViews ($id)
	{
		$sql = "SELECT 
					viewid
				FROM combinationview
				WHERE combinationid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['viewid'];
		}
		return $Data;
	}

	public function addCombinationView ($Data, $id)
	{
		foreach ($Data as $value){
			$sql = 'INSERT INTO combinationview (combinationid, viewid)
					VALUES (:combinationid, :viewid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('combinationid', $id);
			$stmt->bindValue('viewid', $value);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->registry->core->getMessage('ERR_COMBINATION_ADD'), 4, $e->getMessage());
			}
		}
	}
}