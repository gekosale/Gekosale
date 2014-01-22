<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2011 Gekosale. Zabronione jest usuwanie informacji o
 * licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 279 $
 * $Author: gekosale $
 * $Date: 2011-07-28 23:13:43 +0200 (Cz, 28 lip 2011) $
 * $Id: product.php 279 2011-07-28 21:13:43Z gekosale $
 */
namespace Gekosale\Plugin;

class SubiektModel extends FirmesModel
{

	public function getOrderById ($request)
	{
		return App::getModel('firmes/orders')->getOrderById($request['id']);
	}

	public function getOrderList ($request = Array())
	{
		return App::getModel('firmes/orders')->getOrderList($request);
	}

	public function syncOrders ($request)
	{
		foreach ($request['orders'] as $key => $val){
			$sql = 'UPDATE `order` SET firmesid = :subiektdokid WHERE idorder = :id';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('subiektdokid', $val['subiektdokid']);
			$stmt->bindValue('id', $val['orderid']);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				mail('adam@gekosale.com', ' Exception', $e->getMessage());
			}
		}
		return Array(
			'total' => count($request['orders'])
		);
	}

	public function syncOrderStatuses ($request)
	{
		$statuses = $this->registry->core->loadModuleSettings('subiektgt', 0);
		foreach ($request['statuses'] as $key => $val){
			if (isset($statuses['subiekt_status_' . $val['statusid']])){
				$sql = 'SELECT * FROM `order` WHERE idorder = :id';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('id', $val['order']);
				$stmt->execute();
				$rs = $stmt->fetch();
				if ($rs){
					$sql = 'UPDATE `order` SET orderstatusid = :orderstatusid WHERE idorder = :id';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('orderstatusid', $statuses['subiekt_status_' . $val['statusid']]);
					$stmt->bindValue('id', $val['order']);
					$stmt->execute();
					
					$sql = 'INSERT INTO orderhistory(content,adddate, orderstatusid, orderid, inform)
						VALUES (:content,:adddate, :orderstatusid, :orderid, :inform)';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('adddate', $val['date']);
					$stmt->bindValue('content', $val['comments']);
					$stmt->bindValue('orderstatusid', $statuses['subiekt_status_' . $val['statusid']]);
					$stmt->bindValue('orderid', $val['order']);
					$stmt->bindValue('inform', 0);
					$stmt->execute();
				}
			}
		}
		return Array(
			'total' => count($request['statuses'])
		);
	}

	public function addProduct ($request)
	{
		$id = App::getModel('firmes/products')->addUpdateProduct($request['product'][0]);
		return $id;
	}

	public function addPhoto ($request)
	{
		$id = App::getModel('firmes/products')->addProductPhoto($request['photo'][0]);
		
		return Array(
			'id' => $id
		);
	}

	protected function syncPrices ($id, $ean, $price)
	{
		$sql = 'UPDATE product SET
					sellprice = :sellprice
				WHERE idproduct = :idproduct';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idproduct', $id);
		$stmt->bindValue('sellprice', $price);
		$stmt->execute();
		
		$sql = 'UPDATE productattributeset, product SET
					productattributeset.attributeprice = :sellprice,
					productattributeset.value = :sellprice,
					productattributeset.suffixtypeid = 4
				WHERE productattributeset.productid = :idproduct AND product.idproduct = :idproduct AND productattributeset.symbol = :ean';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idproduct', $id);
		$stmt->bindValue('ean', $ean);
		$stmt->bindValue('sellprice', $price);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			mail('adam@gekosale.com', ' Exception', $e->getMessage());
		}
	}

	protected function syncGroupPrices ($id, $ean, $prices)
	{
		$sql = 'DELETE FROM productgroupprice WHERE productid = :productid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productid', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			mail('adam@gekosale.com', ' Exception', $e->getMessage());
		}
		
		foreach ($prices as $clientgroupid => $pricenetto){
			
			$sql = 'INSERT INTO productgroupprice SET
						productid = :productid,
						clientgroupid = :clientgroupid,
						groupprice = :groupprice,
						sellprice = :sellprice,
						promotion = :promotion,
						discountprice = :discountprice,
						promotionstart = :promotionstart,
						promotionend = :promotionend';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $id);
			$stmt->bindValue('clientgroupid', $clientgroupid);
			$stmt->bindValue('groupprice', 1);
			$stmt->bindValue('sellprice', $pricenetto);
			$stmt->bindValue('promotion', 0);
			$stmt->bindValue('discountprice', 0);
			$stmt->bindValue('promotionstart', NULL);
			$stmt->bindValue('promotionend', NULL);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				mail('adam@gekosale.com', ' Exception', $e->getMessage());
			}
		}
	}

	protected function syncStock ($id, $ean, $attributesetid, $stock)
	{
		$sql = 'UPDATE product SET
					stock = :stock
				WHERE idproduct = :idproduct';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idproduct', $id);
		$stmt->bindValue('stock', $stock);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			mail('adam@gekosale.com', ' Exception', $e->getMessage());
		}
		
		if ($attributesetid > 0){
			$sql = 'UPDATE productattributeset, product SET
					productattributeset.stock = :stock
				WHERE productattributeset.idproductattributeset = :idproductattributeset';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('idproductattributeset', $attributesetid);
			$stmt->bindValue('stock', $stock);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				mail('adam@gekosale.com', ' Exception', $e->getMessage());
			}
		}
	}

	public function getClientGroupAll ()
	{
		$sql = 'SELECT 
					clientgroupid AS id, 
					name
				FROM clientgrouptranslation
				WHERE languageid= :languageid
				ORDER BY name ASC';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			mail('adam@gekosale.com', ' Exception', $e->getMessage());
		}
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $rs['name']
			);
		}
		return $Data;
	}

	public function syncProducts ($request)
	{
		Db::getInstance()->beginTransaction();
		
		$settings = $this->registry->core->loadModuleSettings('subiektgt', 0);
		
		$clientGroups = $this->getClientGroupAll();
		
		foreach ($request['products'] as $key => $product){
			
			$sql = "SELECT
						idproduct as id
					FROM product
				WHERE idproduct = :idproduct";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('idproduct', $product['id']);
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				
				if (isset($product['name']) || isset($product['shortdescription']) || isset($product['description'])){
					$sql = 'UPDATE producttranslation SET ';
					if (isset($product['name'])){
						$sql .= 'name = :name,';
					}
					if (isset($product['shortdescription'])){
						$sql .= 'shortdescription = :shortdescription,';
					}
					if (isset($product['description'])){
						$sql .= 'description = :description,';
					}
					$sql .= 'longdescription = longdescription WHERE productid = :productid AND languageid = :languageid';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('productid', $product['id']);
					if (isset($product['name'])){
						$stmt->bindValue('name', base64_decode($product['name']));
					}
					if (isset($product['shortdescription'])){
						$stmt->bindValue('shortdescription', base64_decode($product['shortdescription']));
					}
					if (isset($product['description'])){
						$stmt->bindValue('description', base64_decode($product['description']));
					}
					$stmt->bindValue('languageid', Helper::getLanguageId());
					$stmt->execute();
				}
				
				if (isset($product['categories']) && ! empty($product['categories'])){
					App::getModel('firmes/products')->addUpdateCategory($product['categories'], $product['id']);
				}
				
				if (isset($product['pricenetto'])){
					$this->syncPrices($product['id'], $product['ean'], str_replace(',', '.', $product['pricenetto']));
				}
				
				if (isset($product['prices'])){
					
					$Prices = Array();
					foreach ($product['prices'] as $price){
						if (isset($settings['subiekt_price_' . $price['number']])){
							$Prices[$settings['subiekt_price_' . $price['number']]] = $price['pricenetto'];
						}
					}
					
					$groupPrices = Array();
					foreach ($clientGroups as $group){
						$clientgroupid = $group['id'];
						if (isset($Prices[$clientgroupid])){
							$groupPrices[$clientgroupid] = $Prices[$clientgroupid];
						}
					}
					$this->syncGroupPrices($product['id'], $product['ean'], $groupPrices);
				}
				
				if (isset($product['stock'])){
					if(!isset($product['attributesetid'])){
						$product['attributesetid'] = 0;
					}
					$this->syncStock($product['id'], $product['ean'], $product['attributesetid'], str_replace(',', '.', $product['stock']));
				}
			}
		}

		App::getModel('order')->syncStock();
		
		Db::getInstance()->commit();
		
		return Array(
			'total' => count($request['products'])
		);
	}

	public function getProductById ($request)
	{
	}

	public function getProductList ($request = Array())
	{
		try{
			$this->autoGenerateSymbols();
			
			$limit = isset($request['limit']) ? $request['limit'] : 100;
			$offset = isset($request['offset']) ? $request['offset'] : 0;
			$orderBy = isset($request['orderby']) ? $request['orderby'] : 'adddate';
			$orderDir = isset($request['orderdir']) ? $request['orderdir'] : 'desc';
			$dataset = App::getModel('firmes/products')->getDataset();
			$dataset->setPagination($limit);
			$dataset->setCurrentPage(ceil($offset / $limit) + 1);
			$dataset->setOrderBy('adddate', $orderBy);
			$dataset->setOrderDir('desc', $orderDir);
			$dataset->setSQLParams(Array(
				'filterdate' => isset($request['filterdate']) ? $request['filterdate'] : '2000-01-01 00:00:00',
				'currencysymbol' => 'PLN'
			));
			$products = App::getModel('firmes/products')->getProductDataset();
			return $products['rows'];
		}
		catch (Exception $e){
			return $e->getMessage();
		}
	}

	public function getProductListTotal ($request = Array())
	{
		try{
			$limit = isset($request['limit']) ? $request['limit'] : 100;
			$offset = isset($request['offset']) ? $request['offset'] : 0;
			$orderBy = isset($request['orderby']) ? $request['orderby'] : 'adddate';
			$orderDir = isset($request['orderdir']) ? $request['orderdir'] : 'desc';
			$dataset = App::getModel('firmes/products')->getDataset();
			$dataset->setPagination($limit);
			$dataset->setCurrentPage(ceil($offset / $limit));
			$dataset->setOrderBy('adddate', $orderBy);
			$dataset->setOrderDir('desc', $orderDir);
			$dataset->setSQLParams(Array(
				'filterdate' => isset($request['filterdate']) ? $request['filterdate'] : '2000-01-01 00:00:00',
				'currencysymbol' => 'PLN'
			));
			
			$products = App::getModel('firmes/products')->getProductDataset();
			return Array(
				'total' => $products['total']
			);
		}
		catch (Exception $e){
			return $e->getMessage();
		}
	}

	public function addEmptyGroup ($name)
	{
		$sql = "SELECT idattributegroupname FROM attributegroupname WHERE name = :name";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $name);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			mail('adam@gekosale.com', ' Exception', $e->getMessage());
		}
		$rs = $stmt->fetch();
		if ($rs){
			return $rs['idattributegroupname'];
		}
		else{
			$sql = 'INSERT INTO attributegroupname(name) VALUES (:name)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $name);
			$stmt->execute();
			return Db::getInstance()->lastInsertId();
		}
	}

	public function addNewAttributeProduct ($name)
	{
		$sql = "SELECT idattributeproduct FROM attributeproduct WHERE name = :name";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $name);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			mail('adam@gekosale.com', ' Exception', $e->getMessage());
		}
		$rs = $stmt->fetch();
		if ($rs){
			return $rs['idattributeproduct'];
		}
		else{
			$sql = 'INSERT INTO attributeproduct(name) VALUES (:name)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $name);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				mail('adam@gekosale.com', ' Exception', $e->getMessage());
			}
			return Db::getInstance()->lastInsertId();
		}
	}

	public function addAttributeToGroup ($attributeId, $groupId)
	{
		$sql = "SELECT idattributegroup FROM attributegroup WHERE attributegroupnameid = :attributegroupnameid AND attributeproductid = :attributeproductid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('attributegroupnameid', $groupId);
		$stmt->bindValue('attributeproductid', $attributeId);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			mail('adam@gekosale.com', ' Exception', $e->getMessage());
		}
		$rs = $stmt->fetch();
		if ($rs){
			return $rs['idattributegroup'];
		}
		else{
			$sql = 'INSERT INTO attributegroup(attributegroupnameid, attributeproductid)
					VALUES (:attributegroupnameid, :attributeproductid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('attributegroupnameid', $groupId);
			$stmt->bindValue('attributeproductid', $attributeId);
			$stmt->execute();
			return Db::getInstance()->lastInsertId();
		}
	}

	public function addAttributeValues ($name, $attributeproductid)
	{
		$sql = "SELECT idattributeproductvalue FROM attributeproductvalue WHERE name = :name AND attributeproductid = :attributeproductid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $name);
		$stmt->bindValue('attributeproductid', $attributeproductid);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			mail('adam@gekosale.com', ' Exception', $e->getMessage());
		}
		$rs = $stmt->fetch();
		if ($rs){
			return $rs['idattributeproductvalue'];
		}
		else{
			$sql = 'INSERT INTO attributeproductvalue(name, attributeproductid) VALUES (:name, :attributeproductid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $name);
			$stmt->bindValue('attributeproductid', $attributeproductid);
			$stmt->execute();
			return Db::getInstance()->lastInsertId();
		}
	}

	public function addVariant ($idproduct, $variants, $atributeproductvalues, $attributegroupnameid)
	{
		$sql = "SELECT idproductattributeset FROM productattributeset WHERE firmesid = :firmesid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('firmesid', $variants['id']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			mail('adam@gekosale.com', ' Exception', $e->getMessage());
		}
		$rs = $stmt->fetch();
		if ($rs){
			
			$sql = 'UPDATE productattributeset SET
						stock = :stock,
						symbol = :symbol,
						status = :status,
						weight = :weight,
						suffixtypeid = :suffixtypeid,
						value = :value,
						attributegroupnameid = :attributegroupnameid,
						availablityid = :availablityid,
						photoid = :photoid
					WHERE firmesid = :firmesid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('firmesid', $variants['id']);
			$stmt->bindValue('stock', $variants['stock']);
			$stmt->bindValue('suffixtypeid', 4);
			$stmt->bindValue('value', $variants['pricenetto']);
			$stmt->bindValue('symbol', $variants['ean']);
			$stmt->bindValue('status', 1);
			$stmt->bindValue('weight', $variants['weight']);
			$stmt->bindValue('availablityid', NULL);
			$stmt->bindValue('photoid', NULL);
			$stmt->bindValue('attributegroupnameid', $attributegroupnameid);
			$stmt->execute();
			
			return $rs['idproductattributeset'];
		}
		else{
			$sql = 'INSERT INTO productattributeset (
						productid, 
						stock,
						symbol, 
						status, 
						weight, 
						suffixtypeid, 
						value, 
						attributegroupnameid,
						availablityid,
						photoid,
						firmesid
					)
					VALUES 
					(
						:productid, 
						:stock,
						:symbol, 
						:status, 
						:weight,
						:suffixtypeid, 
						:value, 
						:attributegroupnameid,
						:availablityid,
						:photoid,
						:firmesid
					)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('firmesid', $variants['id']);
			$stmt->bindValue('productid', $idproduct);
			$stmt->bindValue('stock', $variants['stock']);
			$stmt->bindValue('suffixtypeid', 4);
			$stmt->bindValue('value', $variants['pricenetto']);
			$stmt->bindValue('symbol', $variants['ean']);
			$stmt->bindValue('status', 1);
			$stmt->bindValue('weight', $variants['weight']);
			$stmt->bindValue('availablityid', NULL);
			$stmt->bindValue('photoid', NULL);
			$stmt->bindValue('attributegroupnameid', $attributegroupnameid);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				mail('adam@gekosale.com', ' Exception', $e->getMessage());
			}
			$id = Db::getInstance()->lastInsertId();
			
			foreach ($atributeproductvalues as $key => $variant){
				$sql = 'INSERT INTO productattributevalueset (attributeproductvalueid, productattributesetid)
						VALUES (:attributeproductvalueid, :productattributesetid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('attributeproductvalueid', $variant);
				$stmt->bindValue('productattributesetid', $id);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					mail('adam@gekosale.com', ' Exception', $e->getMessage());
				}
			}
			return $id;
		}
	}
} 