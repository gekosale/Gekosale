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
 * $Revision: 484 $
 * $Author: gekosale $
 * $Date: 2011-09-07 13:42:04 +0200 (Śr, 07 wrz 2011) $
 * $Id: feeds.php 484 2011-09-07 11:42:04Z gekosale $
 */

namespace Gekosale;

class FeedsController extends Component\Controller\Frontend
{

	public function index ()
	{
		$type = $this->registry->core->getParam();
		if ($this->registry->core->getParam() != NULL){
			if (method_exists($this, $type) == true){
				$Data = call_user_func(Array(
					$this,
					$type
				));
				$this->registry->template->assign('dataset', $Data['rows']);
				$this->registry->template->assign('date_generated', date("Y-m-d H:i:s"));
				$this->registry->template->display($this->loadTemplate($type . '.tpl'));
			}
		}
	}

	public function productpromotion ()
	{
		$dataset = App::getModel('productpromotion')->getDataset();
		$dataset->setPagination(10);
		$dataset->setOrderBy('name', 'name');
		$dataset->setOrderDir('asc', 'asc');
		$dataset->setCurrentPage(0);
		$products = App::getModel('productpromotion')->getProductDataset();
		return $products;
	}

	public function productnews ()
	{
		$dataset = App::getModel('productnews')->getDataset();
		$dataset->setPagination(10);
		$dataset->setOrderBy('name', 'name');
		$dataset->setOrderDir('asc', 'asc');
		$dataset->setCurrentPage(0);
		$products = App::getModel('productnews')->getProductDataset();
		return $products;
	}

	public function news ()
	{
		return Array(
			'rows' => App::getModel('News')->getNews()
		);
	}
}
?>