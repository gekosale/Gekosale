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
 * $Id: categorylist.php 627 2012-01-20 22:05:57Z gekosale $
 */
namespace Gekosale;

class WebApiModel extends Component\Model
{
	
	/*
	 * getProduct
	 */
	public function getProduct ($id)
	{
		try{
			$limit = isset($request['limit']) ? $request['limit'] : 100;
			$offset = isset($request['offset']) ? $request['offset'] : 0;
			$orderBy = isset($request['orderby']) ? $request['orderby'] : 'adddate';
			$orderDir = isset($request['orderdir']) ? $request['orderdir'] : 'desc';
			$dataset = App::getModel('webapi/products')->getDataset();
			$dataset->setPagination($limit);
			$dataset->setCurrentPage(ceil($offset / $limit) + 1);
			$dataset->setOrderBy('adddate', $orderBy);
			$dataset->setOrderDir('desc', $orderDir);
			$dataset->setSQLParams(Array(
				'id' => $id,
				'currencysymbol' => 'PLN'
			));
			$products = App::getModel('webapi/products')->getProductDataset();
			return current($products['rows']);
		}
		catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	/*
	 * getProducts
	 */
	public function getProducts ($request = Array())
	{
		try{
			$limit = isset($request['limit']) ? $request['limit'] : 100;
			$offset = isset($request['offset']) ? $request['offset'] : 0;
			$orderBy = isset($request['orderby']) ? $request['orderby'] : 'adddate';
			$orderDir = isset($request['orderdir']) ? $request['orderdir'] : 'desc';
			$dataset = App::getModel('webapi/products')->getDataset();
			$dataset->setPagination($limit);
			$dataset->setCurrentPage(ceil($offset / $limit) + 1);
			$dataset->setOrderBy('adddate', $orderBy);
			$dataset->setOrderDir('desc', $orderDir);
			$dataset->setSQLParams(Array(
				'id' => 0,
				'currencysymbol' => 'PLN'
			));
			$products = App::getModel('webapi/products')->getProductDataset();
			return $products['rows'];
		}
		catch (Exception $e){
			return $e->getMessage();
		}
	}

	public function addProduct ($request)
	{
		return App::getModel('webapi/products')->addProduct($request);
	}

	public function updateProduct ($request)
	{
		return App::getModel('webapi/products')->updateProduct($request);
	}
	
	/*
	 * deleteProduct
	 */
	public function deleteProduct ($id)
	{
		$sql = "SELECT COUNT(productid) as total FROM `orderproduct` WHERE productid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs['total'] == 0){
			DbTracker::deleteRows('product', 'idproduct', $id);
			return Array(
				'success' => true
			);
		}
		else{
			return Array(
				'success' => false,
				'message' => 'Ten produkt występuje w zamówieniach'
			);
		}
	}
	
	/*
	 * getCategory
	 */
	public function getCategory ($id)
	{
		return App::getModel('webapi/categories')->getCategory($id);
	}
	
	/*
	 * getCategories
	 */
	public function getCategories ()
	{
		return App::getModel('webapi/categories')->getCategories();
	}
	
	/*
	 * getCategoriesTree
	 */
	public function getCategoriesTree ()
	{
		return App::getModel('webapi/categories')->getCategoriesTree();
	}
	
	/*
	 * addCategory
	 */
	public function addCategory ($request)
	{
		return App::getModel('webapi/categories')->addCategory($request);
	}
	
	/*
	 * updateCategory
	 */
	public function updateCategory ($request)
	{
		return App::getModel('webapi/categories')->updateCategory($request);
	}
	
	/*
	 * deleteCategory
	 */
	public function deleteCategory ($id)
	{
		return App::getModel('webapi/categories')->deleteCategory($id);
	}
	
	/*
	 * Producenci
	 */
	public function getProducer ($id)
	{
		return App::getModel('webapi/producers')->getProducer($id);
	}

	public function getProducers ($request = Array())
	{
		return App::getModel('webapi/producers')->getProducers();
	}

	public function addProducer ($request)
	{
		return App::getModel('webapi/producers')->addProducer($request);
	}

	public function updateProducer ($request)
	{
		return App::getModel('webapi/producers')->updateProducer($request);
	}

	public function deleteProducer ($id)
	{
		return App::getModel('webapi/producers')->deleteProducer($id);
	}
	
	/*
	 * Zdjęcia
	 */
	public function getPhoto ($id)
	{
	}

	public function getPhotos ($id)
	{
		return Array(
			'small' => App::getModel('gallery')->getImagePath(App::getModel('gallery')->getSmallImageById($id)),
			'normal' => App::getModel('gallery')->getImagePath(App::getModel('gallery')->getNormalImageById($id)),
			'large' => App::getModel('gallery')->getImagePath(App::getModel('gallery')->getLargeImageById($id)),
			'orginal' => App::getModel('gallery')->getImagePath(App::getModel('gallery')->getOrginalImageById($id))
		);
	}
	
	/*
	 * getLanguages
	 */
	public function getLanguages ()
	{
		$sql = 'SELECT 
					idlanguage AS id, 
					flag, 
					translation,
					viewid
				FROM language L
				INNER JOIN languageview LV ON LV.languageid = L.idlanguage AND LV.viewid = :viewid';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = Array(
				'id' => $rs['id'],
				'flag' => $rs['flag'],
				'weight' => $rs['id'],
				'icon' => $rs['flag'],
				'name' => $this->trans($rs['translation']),
				'active' => 0
			);
		}
		
		$Data[Helper::getLanguageId()]['active'] = 1;
		return $Data;
	}
	
	/*
	 * getCurrencies
	 */
	public function getCurrencies ()
	{
		$shopCurrencyId = App::getContainer()->get('session')->getActiveCurrencyId();
		$sql = "SELECT 
					*
				FROM currency C";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	/*
	 * Zamówienia
	 */
	public function getOrder ($id)
	{
		return App::getModel('webapi/orders')->getOrder($id);
	}

	public function getOrders ($request)
	{
		return App::getModel('webapi/orders')->getOrders($request);
	}

	public function addOrder ($request)
	{
		return App::getModel('webapi/orders')->addOrder($request);
	}

	public function updateOrder ($request)
	{
		return App::getModel('webapi/orders')->updateOrder($request);
	}

	public function deleteOrder ($id)
	{
		return App::getModel('webapi/orders')->deleteOrder($id);
	}

	public function changeOrderStatus ($request)
	{
		return App::getModel('webapi/orders')->changeOrderStatus($request);
	}
	
	/*
	 * Klienci
	 */
	public function getClient ($id)
	{
		return App::getModel('webapi/clients')->getClient($id);
	}

	public function getClients ($request = Array())
	{
		return App::getModel('webapi/clients')->getClients($request);
	}

	public function addClient ($request)
	{
		return App::getModel('webapi/clients')->addClient($request);
	}

	public function updateClient ($request)
	{
		return App::getModel('webapi/clients')->updateClient($request);
	}

	public function deleteClient ($id)
	{
		return App::getModel('webapi/clients')->deleteClient($id);
	}

	public function disableClient ($id)
	{
		return App::getModel('webapi/clients')->disableClient($id);
	}
	
	/*
	 * General
	 */
}