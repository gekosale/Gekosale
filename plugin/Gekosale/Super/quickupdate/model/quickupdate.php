<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 *
 * $Revision: 385 $
 * $Author: gekosale $
 * $Date: 2011-08-16 16:01:18 +0200 (Wt, 16 sie 2011) $
 * $Id: exchange.php 385 2011-08-16 14:01:18Z gekosale $
 */
namespace Gekosale;

class QuickupdateModel extends Component\Model
{

	protected function getAvailablityIds ()
	{
		$Data = Array();
		$sql = "SELECT
	    			AT.availablityid AS id,
					AT.name AS availablity
				FROM availablitytranslation AT
				WHERE AT.languageid = :languageid";
		
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		
		while ($rs = $stmt->fetch()){
			$Data[$rs['availablity']] = $rs['id'];
		}
		return $Data;
	}

	public function importProducts ($file)
	{
		$availablityIds = $this->getAvailablityIds();
		$Mapping = Array();
		$Data = Array();
		@set_time_limit(0);
		if (($handle = fopen(ROOTPATH . 'upload' . DS . $file, "r")) === FALSE)
			return;
		while (($cols = fgetcsv($handle, 1000, ",")) !== FALSE){
			if ($cols[0] == 'ean'){
				$headers = $cols;
				foreach ($cols as $key => $header){
					$header = trim($header);
					switch ($header) {
						case 'ean':
							$Mapping['ean'] = $key;
							break;
						case 'sellprice':
							$Mapping['sellprice'] = $key;
							break;
						case 'stock':
							$Mapping['stock'] = $key;
							break;
						case 'availablity':
							$Mapping['availablity'] = $key;
							break;
					}
				}
			}
		}
		
		if (! isset($Mapping['ean'])){
			return 0;
		}
		
		if (($handle = fopen(ROOTPATH . 'upload' . DS . $file, "r")) === FALSE)
			return;
		
		while (($cols = fgetcsv($handle, 1000, ",")) !== FALSE){
			if ($cols[0] != 'ean'){
				$Data[] = $cols;
			}
		}
		if(empty($Data)){
			return 0;
		}
		Db::getInstance()->beginTransaction();
		
		foreach ($Data as $key => $product){
			if (count($product) == count($Mapping) && isset($product[$Mapping['ean']]) && strlen($product[$Mapping['ean']]) > 0){
				
				$sql = 'UPDATE product SET ';
				if (isset($Mapping['sellprice']) && isset($product[$Mapping['sellprice']]) && strlen($product[$Mapping['sellprice']]) > 0){
					$sql .= 'sellprice = :sellprice,';
				}
				if (isset($Mapping['stock']) && isset($product[$Mapping['stock']]) && strlen($product[$Mapping['stock']]) > 0){
					$sql .= 'stock = :stock,';
				}
				if (isset($Mapping['availablity']) && isset($product[$Mapping['availablity']]) && strlen($product[$Mapping['availablity']]) > 0 && isset($availablityIds[$product[$Mapping['availablity']]])){
					$sql .= 'availablityid = :availablity';
				}
				else{
					$sql .= 'availablityid = availablityid';
				}
				$sql .= ' WHERE ean = :ean';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('ean', $product[$Mapping['ean']]);
				if (isset($Mapping['sellprice']) && isset($product[$Mapping['sellprice']]) && strlen($product[$Mapping['sellprice']]) > 0){
					$stmt->bindValue('sellprice', $product[$Mapping['sellprice']]);
				}
				if (isset($Mapping['stock']) && isset($product[$Mapping['stock']]) && strlen($product[$Mapping['stock']]) > 0){
					$stmt->bindValue('stock', $product[$Mapping['stock']]);
				}
				if (isset($Mapping['availablity']) && isset($product[$Mapping['availablity']]) && strlen($product[$Mapping['availablity']]) > 0 && isset($availablityIds[$product[$Mapping['availablity']]])){
					$stmt->bindValue('availablity', $availablityIds[$product[$Mapping['availablity']]]);
				}
				$stmt->execute();
				
				$sql = 'UPDATE productattributeset, product SET ';
				if (isset($Mapping['stock']) && isset($product[$Mapping['stock']]) && strlen($product[$Mapping['stock']]) > 0){
					$sql .= 'productattributeset.stock = :stock,';
				}
				if (isset($Mapping['sellprice']) && isset($product[$Mapping['sellprice']]) && strlen($product[$Mapping['sellprice']]) > 0){
					$sql .= 'productattributeset.value =
							 CASE
								 WHEN (productattributeset.suffixtypeid = 1) THEN ROUND((:sellprice / product.sellprice) * 100, 2)
								 WHEN (productattributeset.suffixtypeid = 2) THEN ROUND(:sellprice - product.sellprice,2)
								 WHEN (productattributeset.suffixtypeid = 3) THEN ROUND(product.sellprice - :sellprice,2)
								 WHEN (productattributeset.suffixtypeid = 4) THEN ROUND(:sellprice,2)
							 END,';
				}
				if (isset($Mapping['availablity']) && isset($product[$Mapping['availablity']]) && strlen($product[$Mapping['availablity']]) > 0 && isset($availablityIds[$product[$Mapping['availablity']]])){
					$sql .= 'productattributeset.availablityid = :availablity';
				}
				else{
					$sql .= 'productattributeset.availablityid = productattributeset.availablityid';
				}
				
				$sql .= ' WHERE productattributeset.productid = product.idproduct AND productattributeset.symbol = :ean';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('ean', $product[$Mapping['ean']]);
				if (isset($Mapping['sellprice']) && isset($product[$Mapping['sellprice']]) && strlen($product[$Mapping['sellprice']]) > 0){
					$stmt->bindValue('sellprice', $product[$Mapping['sellprice']]);
				}
				if (isset($Mapping['stock']) && isset($product[$Mapping['stock']]) && strlen($product[$Mapping['stock']]) > 0){
					$stmt->bindValue('stock', $product[$Mapping['stock']]);
				}
				if (isset($Mapping['availablity']) && isset($product[$Mapping['availablity']]) && strlen($product[$Mapping['availablity']]) > 0 && isset($availablityIds[$product[$Mapping['availablity']]])){
					$stmt->bindValue('availablity', $availablityIds[$product[$Mapping['availablity']]]);
				}
				$stmt->execute();
			}
		}
		
		App::getModel('product')->syncStock();
		$this->updateProductAttributesetPricesAll();
		Db::getInstance()->commit();
		return count($Data);
	}

	public function updateProductAttributesetPricesAll ()
	{
		$sql = 'UPDATE productattributeset, product SET
					productattributeset.attributeprice =
					CASE
						WHEN (productattributeset.suffixtypeid = 1) THEN ROUND(product.sellprice * (productattributeset.value / 100), 4)
						WHEN (productattributeset.suffixtypeid = 2) THEN ROUND(product.sellprice + productattributeset.value,4)
						WHEN (productattributeset.suffixtypeid = 3) THEN ROUND(product.sellprice - productattributeset.value,4)
						WHEN (productattributeset.suffixtypeid = 4) THEN ROUND(productattributeset.value,4)
					END,
					productattributeset.discountprice =
					IF(product.promotion = 1,
					CASE
						WHEN (productattributeset.suffixtypeid = 1) THEN ROUND(product.discountprice * (productattributeset.value / 100), 4)
						WHEN (productattributeset.suffixtypeid = 2) THEN ROUND(product.discountprice + productattributeset.value,4)
						WHEN (productattributeset.suffixtypeid = 3) THEN ROUND(product.discountprice - productattributeset.value,4)
						WHEN (productattributeset.suffixtypeid = 4) THEN ROUND(productattributeset.value,4)
					END, NULL)
				WHERE productattributeset.productid = product.idproduct';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
	}

	public function exportProducts ()
	{
		$Data = Array();
		
		$sql = 'SELECT
	    			P.ean as ean,
	    			ROUND(P.sellprice,2) as sellprice,
	    			P.stock as stock,
					AT.name AS availablity
				FROM product P
				LEFT JOIN availablity A ON A.idavailablity = P.availablityid
				LEFT JOIN availablitytranslation AT ON AT.availablityid = P.availablityid AND AT.languageid = :languageid 
		        LEFT JOIN productcategory PC ON PC.productid = P.idproduct
                LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid
				WHERE
					P.ean != \'\' AND VC.viewid IN (' . Helper::getViewIdsAsString() . ')
				GROUP BY P.idproduct';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'ean' => $rs['ean'],
				'sellprice' => $rs['sellprice'],
				'stock' => $rs['stock'],
				'availablity' => $rs['availablity']
			);
		}
		
		$sql = 'SELECT
	    			PAS.symbol AS ean,
	    			PAS.attributeprice as sellprice,
	    			PAS.stock,
					AT.name AS availablity
				FROM productattributeset PAS
		        LEFT JOIN product P ON PAS.productid = P.idproduct
		        LEFT JOIN productcategory PC ON PC.productid = P.idproduct
                LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid
				LEFT JOIN availablitytranslation AT ON PAS.availablityid = AT.availablityid AND AT.languageid = :languageid
				WHERE
					PAS.symbol != \'\' AND VC.viewid IN (' . Helper::getViewIdsAsString() . ')
				GROUP BY PAS.idproductattributeset';
		
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'ean' => $rs['ean'],
				'sellprice' => $rs['sellprice'],
				'stock' => $rs['stock'],
				'availablity' => $rs['availablity']
			);
		}
		
		$filename = 'products_' . date('Y_m_d_H_i_s') . '.csv';
		if (isset($Data[0])){
			$header = array_keys($Data[0]);
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			$fp = fopen("php://output", 'w');
			fputcsv($fp, $header);
			foreach ($Data as $key => $values){
				fputcsv($fp, $values);
			}
			fclose($fp);
			exit();
		}
		else{
			return 0;
		}
	}
}
