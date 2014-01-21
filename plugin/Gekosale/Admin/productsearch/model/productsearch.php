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
 * $Id: productsearch.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;
use sfEvent;

class productsearchModel extends Component\Model
{

	public function addProductToSearch ($request)
	{
		$Data = $request['data'];
		$productid = $request['id'];
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO productsearch (productid, languageid, name, shortdescription, description, producername, attributes)
						VALUES (:productid, :languageid, :name, :shortdescription, :description, :producername, :attributes)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $productid);
			$stmt->bindValue('languageid', $key);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('shortdescription', $Data['shortdescription'][$key]);
			$stmt->bindValue('description', $Data['description'][$key]);
			$stmt->bindValue('producername', App::getModel('producer')->getProducerNameById($Data['producerid'], $key));
			if ($Data['variants'] == NULL){
				$stmt->bindValue('attributes', NULL);
			}
			else{
				$stmt->bindValue('attributes', $Data['variants']['set']);
			}
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_SEARCH_INSERT'), 112, $e->getMessage());
			}
		}
	}

	public function updateProductSearch ($request)
	{
		$Data = $request['data'];
		$id = $request['id'];
		
		DbTracker::deleteRows('productsearch', 'productid', $id);
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO productsearch (productid, languageid, name, shortdescription, description, producername, attributes)
						VALUES (:productid, :languageid, :name, :shortdescription, :description, :producername, :attributes)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $id);
			$stmt->bindValue('languageid', $key);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('shortdescription', $Data['shortdescription'][$key]);
			$stmt->bindValue('description', $Data['description'][$key]);
			$stmt->bindValue('producername', App::getModel('producer')->getProducerNameById($Data['producerid'], $key));
			if (! isset($Data['variants']) || $Data['variants'] == NULL){
				$stmt->bindValue('attributes', NULL);
			}
			else{
				$stmt->bindValue('attributes', $Data['variants']['set']);
			}
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_SEARCH_UPDATE'), 112, $e->getMessage());
			}
		}
	}

	protected function ProductSearchStatus ($productid, $status)
	{
		$sql = 'UPDATE productsearch SET enable = :status WHERE productid = :productid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('status', $status);
		$stmt->bindValue('productid', $productid);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function enableProductSearchForProduct ($idproduct)
	{
		$this->ProductSearchStatus($idproduct, 1);
	}

	public function disableProductSearchForProduct ($idproduct)
	{
		$this->ProductSearchStatus($idproduct, 0);
	}

	protected function getAttributeNamesFromProductArray ($Data)
	{
		$Attr = Array();
		foreach ($Data as $index => $attributes){
			foreach ($attributes['attribute'] as $attr){
				$Attr[] = $attr;
			}
		}
		$Attr = App::getModel('attributeproduct')->getAttributeNamesDistinctByArrayId($Attr);
		if (count($Attr) > 0){
			return implode(' ', $Attr);
		}
		return false;
	}
}