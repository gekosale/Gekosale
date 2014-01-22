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
 * $Id: productnews.php 438 2011-08-27 09:29:36Z gekosale $
 */

namespace Gekosale\Plugin;

class ProductNewsModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('productnew', Array(
			'idproductnew' => Array(
				'source' => 'PN.productid'
			),
			'name' => Array(
				'source' => 'PT.name'
			),
			'active' => Array(
				'source' => 'PN.active'
			),
			'startdate' => Array(
				'source' => 'PN.startdate'
			),
			'enddate' => Array(
				'source' => 'PN.enddate'
			),
			'adddate' => Array(
				'source' => 'PN.adddate'
			),
			'categoryname' => Array(
				'source' => 'CT.name'
			),
			'categoryid' => Array(
				'source' => 'PC.categoryid',
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
			productnew PN
			LEFT JOIN product P ON PN.productid = P.idproduct
			LEFT JOIN producttranslation PT ON PN.productid = PT.productid AND PT.languageid = :languageid
			LEFT JOIN productcategory PC ON PC.productid = PN.productid
			LEFT JOIN category C ON C.idcategory = PC.categoryid
			LEFT JOIN categorypath CP ON C.idcategory = CP.categoryid
			LEFT JOIN categorytranslation CT ON C.idcategory = CT.categoryid AND CT.languageid = :languageid
		');

		$datagrid->setGroupBy('
			PN.productid
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

	public function getProductNewsForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteProductNews ($datagrid, $id)
	{
		return $this->getDatagrid()->deleteRow($datagrid, $id, Array(
			$this,
			'deleteProductNews'
		), $this->getName());
	}

	public function deleteProductNews ($id)
	{
		DbTracker::deleteRows('productnew', 'productid', $id);
	}

	public function doAJAXEnableProductNews ($datagridId, $id)
	{
		try{
			$this->enableProductNews($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_ENABLE_PRODUCT_NEWS')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function doAJAXDisableProductNews ($datagridId, $id)
	{
		try{
			$this->disableProductNews($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_DISABLE_PRODUCT_NEWS')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function disableProductNews ($id)
	{
		$sql = 'UPDATE productnew SET active = 0 WHERE productid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function enableProductNews ($id)
	{
		$sql = 'UPDATE productnew SET active = 1 WHERE productid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function addProductNews ($Data)
	{
		foreach($Data['productid'] as $id){
			$sql = 'INSERT INTO productnew (productid, startdate, enddate, adddate, active)
					VALUES (:productid, :startdate, :enddate, :adddate, :active)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $id);
			if ($Data['endnew'] == '' || $Data['endnew'] == '0000-00-00 00:00:00'){
				$stmt->bindValue('enddate', NULL);
			}
			else{
				$stmt->bindValue('enddate', $Data['endnew']);
			}
			if ($Data['startnew'] == '' || $Data['startnew'] == '0000-00-00 00:00:00'){
				$stmt->bindValue('startdate', NULL);
			}
			else{
				$stmt->bindValue('startdate', $Data['startnew']);
			}
			if ($Data['adddate'] == '' || $Data['adddate'] == '0000-00-00 00:00:00'){
				$stmt->bindValue('adddate', date('Y-m-d H:i:s'));
			}
			else {
				$stmt->bindValue('adddate', $Data['adddate']);
			}
			$stmt->bindValue('active', 1);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_NEW_ADD'), 112, $e->getMessage());
			}
		}
	}

	public function getExcludeProducts ()
	{
		$sql = "SELECT
					productid
				FROM productnew
		";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$Data = Array(
			0
		);
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['productid'];
		}
		return $Data;
	}
}