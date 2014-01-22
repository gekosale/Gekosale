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
 * $Revision: 576 $
 * $Author: gekosale $
 * $Date: 2011-10-22 10:23:55 +0200 (So, 22 paź 2011) $
 * $Id: productreview.php 576 2011-10-22 08:23:55Z gekosale $
 */
namespace Gekosale\Plugin;

require_once dirname(__FILE__) . '/storage/db.php';
require_once dirname(__FILE__) . '/storage/session.php';
class ProductCompareModel extends Component\Model
{
	private $storage;
	private $productIds = array();
	const PRODUCT_LIMIT = 5;
	const ERROR_LIMIT_REACHED = - 1;

	public function __construct ($registry)
	{
		parent::__construct($registry);
		
		if (App::getContainer()->get('session')->getActiveClientid() > 0){
			$this->storage = new \Gekosale\Frontend\Productcompare\Storage\DbModel($registry);
		}
		else{
			$this->storage = new \Gekosale\Frontend\Productcompare\Storage\SessionModel($registry);
		}
		;
	}

	public function getCount ()
	{
		return count($this->storage->getProductIds());
	}

	public function getProducts ()
	{
		$this->productIds = $this->storage->getProductIds();
		$productModel = App::getModel('productsearch');
		$dataset = $productModel->getDataset();
		$dataset->setPagination(10);
		$dataset->setCurrentPage(1);
		$dataset->setOrderBy('name', 'name');
		$dataset->setOrderDir('asc', 'asc');
		$dataset->setSQLParams(array(
			'categoryid' => 0,
			'name' => '%',
			'clientid' => App::getContainer()->get('session')->getActiveClientid(),
			'producer' => 0,
			'pricefrom' => 0,
			'priceto' => 99999,
			'filterbyproducer' => 0,
			'enablelayer' => 1,
			'products' => $this->productIds
		));
		$products = $productModel->getProductDataset();
		$products = $products['rows'];
		return $products;
	}

	public function getAttributesTree ()
	{
		if (count($this->productIds) == 0){
			App::redirectSeo($this->registry->router->generate('frontend.home', true));
		}
		$attributesTree = array();
		$technicalData = array();
		$productModel = App::getModel('product');
		
		// Get technical data for products
		foreach ($this->productIds as $productId){
			$technicalData[$productId] = $productModel->GetTechnicalDataForProduct($productId);
		}
		
		// Create attributes matrix
		foreach ($technicalData as $productId => $sections){
			foreach ($sections as $section){
				foreach ($section['attributes'] as $attribute){
					if (! isset($attributesTree[$section['name']][$attribute['name']])){
						$attributesTree[$section['name']][$attribute['name']] = array();
					}
					$attributesTree[$section['name']][$attribute['name']] += array(
						$productId => (! is_null($attribute['value']) && $attribute['value'] != '') ? $attribute['value'] : '-'
					);
				}
			}
		}
		
		$str = '';
		foreach ($this->productIds as $ids){
			$str[] = '-';
		}
		$strCompare = implode('|', $str);
		foreach ($attributesTree as &$section){
			foreach ($section as $name => &$values){
				if (implode('|', array_values($values)) == $strCompare){
					unset($section[$name]);
				}
			}
		}
		return $attributesTree;
	}

	public function addProduct ($productId)
	{
		if ($this->getCount() < self::PRODUCT_LIMIT){
			return $this->storage->addProduct($productId);
		}
		else 
			if ($this->getCount() >= self::PRODUCT_LIMIT){
				return self::ERROR_LIMIT_REACHED;
			}
	}

	public function ajaxAddProduct ($productId)
	{
		$response = new \xajaxResponse();
		
		try{
			if (($error = $this->addProduct($productId)) === TRUE){
				$response->script("window.location.href = '{$this->registry->router->generate('frontend.productcompare', true)}';");
			}
			else 
				if ($error == self::ERROR_LIMIT_REACHED){
					$response->script("GError('Osiągnięto limit produktów w porównywarce.');");
				}
				else{
					$response->script("GError('Wystąpił problem przy dodawaniu produktu do porównywarki.');");
				}
		}
		catch (Exception $e){
			$response->script("GError('Wystąpił problem przy dodawaniu produktu do porównywarki.');");
		}
		
		return $response;
	}

	public function deleteProduct ($productId)
	{
		return $this->storage->deleteProduct($productId);
	}

	public function ajaxDeleteProduct ($productId)
	{
		$response = new \xajaxResponse();
		
		try{
			$this->deleteProduct($productId);
			$response->script('window.location.reload( false )');
		}
		catch (Exception $e){
			$response->script("GError('Wystąpił problem przy usuwaniu produkty z porównywarki.');");
		}
		
		return $response;
	}

	public function deleteAllProducts ()
	{
		return $this->storage->deleteAllProducts();
	}

	public function ajaxDeleteAllProducts ()
	{
		$response = new \xajaxResponse();
		
		try{
			$this->deleteAllProducts();
			$response->script("window.location.href = '{$this->registry->router->generate('frontend.home', true)}'");
		}
		catch (Exception $e){
			$response->script("GError('Wystąpił problem przy usuwaniu produktów z porównywarki.');");
		}
		
		return $response;
	}

	public function ajaxCompareProducts ()
	{
		$response = new \xajaxResponse();
		
		$response->script("$.colorbox({maxWidth:'90%', href:'" . $this->registry->router->generate('frontend.productcompare', true) . "'})");
		
		return $response;
	}
}