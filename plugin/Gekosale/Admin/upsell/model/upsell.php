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
 * $Revision: 627 $
 * $Author: gekosale $
 * $Date: 2012-01-20 23:05:57 +0100 (Pt, 20 sty 2012) $
 * $Id: upsell.php 627 2012-01-20 22:05:57Z gekosale $ 
 */
namespace Gekosale;

class UpsellModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('upsell', Array(
			'idupsell' => Array(
				'source' => 'US.productid'
			),
			'adddate' => Array(
				'source' => 'US.adddate'
			),
			'name' => Array(
				'source' => 'PT.name',
				'prepareForAutosuggest' => true
			),
			'productcount' => Array(
				'source' => 'count(distinct US.relatedproductid)',
				'filter' => 'having'
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
			upsell US
			LEFT JOIN producttranslation PT ON US.productid = PT.productid AND PT.languageid = :languageid
			LEFT JOIN productcategory PC ON PC.productid = PT.productid
			LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid
			LEFT JOIN category C ON C.idcategory = PC.categoryid
			LEFT JOIN categorypath CP ON C.idcategory = CP.categoryid
			LEFT JOIN categorytranslation CT ON C.idcategory = CT.categoryid AND CT.languageid = :languageid
		');
		
		$datagrid->setGroupBy('
			US.productid
		');
		
		if (Helper::getViewId() > 0){
			$datagrid->setAdditionalWhere('
				VC.viewid IN (' . Helper::getViewIdsAsString() . ')
			');
		}
	}

	public function getNameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getUpsellForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteUpsell ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteUpsell'
		), $this->getName());
	}

	public function deleteUpsell ($id)
	{
		DbTracker::deleteRows('upsell', 'productid', $id);
	}

	public function getProductsDataGrid ($id)
	{
		$sql = "SELECT 
					US.productid AS id, 
					US.relatedproductid as idproduct, 
					US.hierarchy AS hierarchy,
					PT.name
 				FROM upsell US
 				LEFT JOIN product P ON P.idproduct = US.relatedproductid
 				LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
				WHERE US.productid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['idproduct'],
				'hierarchy' => $rs['hierarchy']
			);
		}
		return $Data;
	}

	public function getUpsellView ($id)
	{
		$sql = "SELECT CS.productid AS id, PT.name
					FROM upsell CS
					LEFT JOIN product P ON P.idproduct= CS.productid
					LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
					WHERE CS.productid=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'id' => $rs['id'],
				'name' => $rs['name'],
				'relatedproduct' => $this->getUpsellProductView($id)
			);
		}
		else{
			throw new CoreException($this->trans('ERR_UPSELL_NO_EXIST'));
		}
		return $Data;
	}

	public function getUpsellProductView ($id)
	{
		$sql = "SELECT 
					PT.name AS relatedproduct
					FROM upsell CS
					LEFT JOIN product P ON P.idproduct= CS.relatedproductid
					LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
				WHERE CS.productid=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'relatedproduct' => $rs['relatedproduct']
			);
		}
		return $Data;
	}

	public function addNewRelated ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->addUpSell($Data['products'], $Data['productid']);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_UP_SELL_NEW_ADD'), 12, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function addUpSell ($Data, $id)
	{
		foreach ($Data as $value){
			$sql = 'INSERT INTO upsell SET
						productid = :productid, 
						relatedproductid = :relatedproductid,
						hierarchy = :hierarchy
					ON DUPLICATE KEY UPDATE
						hierarchy = :hierarchy';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $id);
			$stmt->bindValue('relatedproductid', $value['id']);
			$stmt->bindValue('hierarchy', $value['hierarchy']);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_UP_SELL_ADD'), 11, $e->getMessage());
			}
		}
	}

	public function editRelated ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->deleteUpSellById($id);
			$this->addUpSell($Data['products'], $id);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_UP_SELL_EDIT'), 10, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function deleteUpSellById ($id)
	{
		DbTracker::deleteRows('upsell', 'productid', $id);
	}
}