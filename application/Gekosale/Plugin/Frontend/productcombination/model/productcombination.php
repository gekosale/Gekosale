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
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: productnews.php 619 2011-12-19 21:09:00Z gekosale $
 */
namespace Gekosale\Plugin;

class ProductCombinationModel extends Component\Model
{

	public function getCombinationListForProduct ($id)
	{
		$sql = "SELECT 
					C.idcombination,
					C.value
            	FROM combination C
				INNER JOIN productcombination PC ON C.idcombination = PC.combinationid
				LEFT JOIN combinationview CV ON C.idcombination = CV.combinationid AND CV.viewid = :viewid
				WHERE PC.productid = :id
				GROUP BY C.idcombination
		";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			
			$products = $this->getCombinationProducts($rs['idcombination'], 1);
			
			if (! empty($products)){
				$Combination = Array(
					'id' => $rs['idcombination'],
					'discount' => $rs['value'],
					'totals' => $products['totals'],
					'products' => $products['products']
				);
				$Data[] = $this->setCombinationData($Combination);
			}
		}
		
		return $Data;
	}

	public function getCombinationById ($id, $qty)
	{
		$products = $this->getCombinationProducts($id, $qty);
		
		if (! empty($products)){
			return $this->setCombinationData(Array(
				'id' => $id,
				'totals' => $products['totals'],
				'products' => $products['products']
			));
		}
	}

	public function getCombinationProducts ($id, $multiplier)
	{
		$sql = "SELECT
					PC.productid,
					PC.numberofitems as qty,
					PC.productattributesetid,
					C.value
            	FROM productcombination PC
				LEFT JOIN combination C ON C.idcombination = PC.combinationid
				WHERE PC.combinationid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		$bStockCheck = true;
		while ($rs = $stmt->fetch()){
			$product = App::getModel('product')->getProductAndAttributesById($rs['productid']);
			App::getModel('product')->getPhotos($product);
			$qty = $rs['qty'] * $multiplier;
			$product['numberofitems'] = $qty;
			
			if (isset($product['attributes']) && (int) $rs['productattributesetid'] > 0){
				$product['attset'] = App::getModel('product')->getProductVariant($product);
				foreach ($product['attset'] as $group => $data){
					if ($group == $rs['productattributesetid']){
						$product['attset'][$group]['sellprice'] = $data['sellprice'] * (1 - ($rs['value'] / 100));
						$product['attset'][$group]['sellpricenetto'] = $data['sellpricenetto'] * (1 - ($rs['value'] / 100));
						
						$totals = Array(
							'discountpricenetto' => $qty * $product['attset'][$group]['sellpricenetto'],
							'discountpricegross' => $qty * $product['attset'][$group]['sellprice'],
							'standardpricenetto' => $qty * $data['sellpricenetto'],
							'standardpricegross' => $qty * $data['sellprice']
						);
						
						if ($this->checkProductQuantity($product['trackstock'], $qty, $data['stock']) == false){
							$bStockCheck = false;
						}
						$product['totals'] = $totals;
					}
				}
				$Data['products'][] = $product;
				$Data['totals'][] = $totals;
			}
			
			if (empty($product['attributes'])){
				$promotionPriceNetto = $product['pricenetto'] * (1 - ($rs['value'] / 100));
				$promotionPriceGross = $product['price'] * (1 - ($rs['value'] / 100));
				
				$totals = Array(
					'discountpricenetto' => $qty * $promotionPriceNetto,
					'discountpricegross' => $qty * $promotionPriceGross,
					'standardpricenetto' => $qty * $product['pricenetto'],
					'standardpricegross' => $qty * $product['price']
				);
				
				$product['totals'] = $totals;
				
				$Data['products'][] = $product;
				$Data['totals'][] = $totals;
				
				if ($this->checkProductQuantity($product['trackstock'], $qty, $product['stock']) == false){
					$bStockCheck = false;
				}
			}
		}
		
		return ($bStockCheck == true) ? $Data : Array();
	}

	public function checkProductQuantity ($trackStock, $qty, $stock)
	{
		if ($trackStock == 0){
			return true;
		}
		else{
			if ($qty > $stock){
				return false;
			}
			else{
				return true;
			}
		}
		return false;
	}

	protected function setCombinationData ($combination)
	{
		$totalDiscountNetto = 0;
		$totalDiscountGross = 0;
		$totalDiscountPriceNetto = 0;
		$totalDiscountPriceGross = 0;
		$totalStandardPriceNetto = 0;
		$totalStandardPriceGross = 0;
		
		foreach ($combination['totals'] as $total){
			$totalDiscountPriceNetto += $total['discountpricenetto'];
			$totalDiscountPriceGross += $total['discountpricegross'];
			$totalStandardPriceNetto += $total['standardpricenetto'];
			$totalStandardPriceGross += $total['standardpricegross'];
		}
		$combination['summary'] = Array(
			'totalDiscountNetto' => $totalStandardPriceNetto - $totalDiscountPriceNetto,
			'totalDiscountGross' => $totalStandardPriceGross - $totalDiscountPriceGross,
			'totalDiscountPriceNetto' => $totalDiscountPriceNetto,
			'totalDiscountPriceGross' => $totalDiscountPriceGross,
			'totalStandardPriceNetto' => $totalStandardPriceNetto,
			'totalStandardPriceGross' => $totalStandardPriceGross
		);
		return $combination;
	}
}