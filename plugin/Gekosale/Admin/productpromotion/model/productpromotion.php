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
 * $Revision: 263 $
 * $Author: gekosale $
 * $Date: 2011-07-24 16:23:40 +0200 (N, 24 lip 2011) $
 * $Id: productnews.php 263 2011-07-24 14:23:40Z gekosale $ 
 */
namespace Gekosale;

class ProductPromotionModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('productnew', Array(
			'idproduct' => Array(
				'source' => 'P.idproduct'
			),
			'name' => Array(
				'source' => 'PT.name'
			),
			'adddate' => Array(
				'source' => 'P.adddate'
			),
			'enddate' => Array(
				'source' => 'IF(PGP.promotionend IS NOT NULL, PGP.promotionend, IF(P.promotionend IS NOT NULL, P.promotionend, NULL))'
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
			product P
			LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
			LEFT JOIN productcategory PC ON PC.productid = P.idproduct
			LEFT JOIN productgroupprice PGP ON PGP.productid = P.idproduct AND PGP.clientgroupid
			LEFT JOIN category C ON C.idcategory = PC.categoryid
			LEFT JOIN categorypath CP ON C.idcategory = CP.categoryid
			LEFT JOIN categorytranslation CT ON C.idcategory = CT.categoryid AND CT.languageid = :languageid
		');
		
		$datagrid->setAdditionalWhere('
			(PGP.promotion = 1 OR P.promotion = 1)
		');
		
		$datagrid->setGroupBy('
			P.idproduct
		');
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getProductPromotionForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteProductPromotion ($datagrid, $id)
	{
		$ids = (is_array($id)) ? $id : (array) $id;
		
		$sql = 'UPDATE product SET 
					promotion = :promotion,
					discountprice = IF(:discount > 0, sellprice - (sellprice * :discount), 0),
					promotionstart = :promotionstart,
					promotionend = :promotionend
				WHERE idproduct IN (' . implode(',', $ids) . ')';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('promotion', 0);
		$stmt->bindValue('discount', 0, NULL);
		$stmt->bindValue('promotionstart', NULL);
		$stmt->bindValue('promotionend', NULL);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCT_UPDATE'), 112, $e->getMessage());
		}
		foreach ($ids as $product){
			$this->deleteProductPromotion($product);
		}
		
		return $this->getDatagrid()->refresh($datagrid);
	}

	public function deleteProductPromotion ($id)
	{
		$productGroupPrice = App::getModel('product')->getProductGroupPrice($id);
		if (! empty($productGroupPrice)){
			DbTracker::deleteRows('productgroupprice', 'productid', $id);
		}
	}

	public function addPromotion ($Data)
	{
		Db::getInstance()->beginTransaction();
		
		$sql = 'DELETE FROM productgroupprice 
				WHERE 
					productid IN (' . implode(',', $Data['productid']) . ') AND
					groupprice = 0 AND 
					promotion = 0';
		$stmt = Db::getInstance()->prepare($sql);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCT_GROUP_PRICE_ADD'), 112, $e->getMessage());
		}
		
		$sql = 'UPDATE productgroupprice SET 
					promotion = :promotion,
					discountprice = :discountprice,
					promotionstart = :promotionstart,
					promotionend = :promotionend
				WHERE productid IN (' . implode(',', $Data['productid']) . ')';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('promotion', 0);
		$stmt->bindValue('discountprice', 0);
		$stmt->bindValue('promotionstart', NULL);
		$stmt->bindValue('promotionend', NULL);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCT_GROUP_PRICE_ADD'), 112, $e->getMessage());
		}
		
		foreach ($Data['productid'] as $key => $idproduct){
			
			$sql = 'UPDATE product SET 
					promotion = :promotion,
					discountprice = IF(:discount > 0, sellprice - (sellprice * :discount), 0),
					promotionstart = :promotionstart,
					promotionend = :promotionend
				WHERE idproduct = :id';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $idproduct);
			if (isset($Data['promotion']) && $Data['promotion'] == 1){
				$stmt->bindValue('promotion', $Data['promotion']);
				$stmt->bindValue('discount', $Data['discount'] / 100);
				if ($Data['promotionstart'] != ''){
					$stmt->bindValue('promotionstart', $Data['promotionstart']);
				}
				else{
					$stmt->bindValue('promotionstart', NULL);
				}
				if ($Data['promotionend'] != ''){
					$stmt->bindValue('promotionend', $Data['promotionend']);
				}
				else{
					$stmt->bindValue('promotionend', NULL);
				}
			}
			else{
				$stmt->bindValue('promotion', 0);
				$stmt->bindValue('discount', 0, NULL);
				$stmt->bindValue('promotionstart', NULL);
				$stmt->bindValue('promotionend', NULL);
			}
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_UPDATE'), 112, $e->getMessage());
			}
			
			$productGroupPrice = App::getModel('product')->getProductGroupPrice($idproduct);
			$clientGroups = App::getModel('clientgroup/clientgroup')->getClientGroupAll();
			foreach ($clientGroups as $key => $group){
				$clientgroupid = $group['id'];
				if (isset($Data['promotion_' . $clientgroupid]) && $Data['promotion_' . $clientgroupid] == 1){
					if (isset($productGroupPrice['groupid_' . $clientgroupid])){
						$sellprice = $productGroupPrice['sellprice_' . $clientgroupid];
					}
					else{
						$product = App::getModel('product')->getProductView($idproduct, false);
						$sellprice = $product['sellprice'];
					}
					$sql = 'INSERT INTO productgroupprice SET
								productid = :productid,
								clientgroupid = :clientgroupid,
								promotion = :promotion,
								discountprice = :discountprice,
								promotionstart = :promotionstart,
								promotionend = :promotionend
							ON DUPLICATE KEY UPDATE
								promotion = :promotion,
								discountprice = :discountprice,
								promotionstart = :promotionstart,
								promotionend = :promotionend
					';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('productid', $idproduct);
					$stmt->bindValue('clientgroupid', $clientgroupid);
					$stmt->bindValue('promotion', 1);
					$stmt->bindValue('discountprice', $sellprice * (1 - ($Data['discount_' . $clientgroupid] / 100)));
					if ($Data['promotionstart_' . $clientgroupid] != ''){
						$stmt->bindValue('promotionstart', $Data['promotionstart_' . $clientgroupid]);
					}
					else{
						$stmt->bindValue('promotionstart', NULL);
					}
					if ($Data['promotionend_' . $clientgroupid] != ''){
						$stmt->bindValue('promotionend', $Data['promotionend_' . $clientgroupid]);
					}
					else{
						$stmt->bindValue('promotionend', NULL);
					}
					try{
						$stmt->execute();
					}
					catch (Exception $e){
						throw new CoreException($this->trans('ERR_PRODUCT_GROUP_PRICE_ADD'), 112, $e->getMessage());
					}
				}
			}
		}
		
		Db::getInstance()->commit();
	}
}