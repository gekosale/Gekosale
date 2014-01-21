<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale. Zabronione jest usuwanie informacji o
 * licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 583 $
 * $Author: gekosale $
 * $Date: 2011-10-28 22:19:07 +0200 (Pt, 28 paź 2011) $
 * $Id: showcasebox.php 583 2011-10-28 20:19:07Z gekosale $
 */
namespace Gekosale;

class ShowcaseBoxController extends Component\Controller\Box
{

	public function __construct ($registry, $box)
	{
		parent::__construct($registry, $box);
		$this->dataset = App::getModel('ShowcaseBox')->getDataset('ShowcaseBox');
	}

	public function index ()
	{
		$this->registry->template->assign('showcasecategories', App::getModel('ShowcaseBox')->getCategories($this->_boxAttributes));
		$this->registry->template->assign('products', $this->getProductsTemplate());
		$this->registry->xajaxInterface->registerFunction(Array(
			'GetProductsForSchowcase_' . $this->_boxId,
			$this,
			'ajax_getProducts'
		));
		return $this->registry->template->fetch($this->loadTemplate('index.tpl'));
	}

	protected function getProductsTemplate ($categoryId = 0)
	{
		$params = $this->_boxAttributes;
		
		if ($params['productsCount'] > 0){
			$this->dataset->setPagination($params['productsCount']);
		}
		$this->dataset->setOrderBy($this->_boxAttributes['orderBy'], $this->_boxAttributes['orderBy']);
		$this->dataset->setOrderDir($this->_boxAttributes['orderDir'], $this->_boxAttributes['orderDir']);
		$this->dataset->setCurrentPage(1);
		$this->dataset->setSQLParams(Array(
			'clientid' => App::getContainer()->get('session')->getActiveClientid(),
			'statusid' => $params['statusId'],
			'category' => $categoryId
		));
		$products = App::getModel('ShowcaseBox')->getProductDataset();
		$this->registry->template->assign('categoryid', $categoryId);
		$this->registry->template->assign('items', $products['rows']);
		$result = $this->registry->template->fetch($this->loadTemplate('item.tpl'));
		return $result;
	}

	public function ajax_getProducts ($request)
	{
		return Array(
			'products' => $this->getProductsTemplate($request['category'])
		);
	}
}