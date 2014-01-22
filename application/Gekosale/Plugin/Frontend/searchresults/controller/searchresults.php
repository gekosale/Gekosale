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
 * $Id: searchresults.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Plugin;

class searchresultsController extends Component\Controller\Frontend
{

	public function index ()
	{
		$param = str_replace('_', '', App::getModel('formprotection')->cropDangerousCode($this->getParam()));

		if (strlen($param) > 2){
			$dataset = App::getModel('searchresults')->getDataset();
			$dataset->setPagination(5);
			$dataset->setCurrentPage(1);
			$dataset->setOrderBy('name', 'name');
			$dataset->setOrderDir('asc', 'asc');
			$dataset->setSQLParams(Array(
				'name' => '%' . str_replace(' ', '%', $param) . '%',
			));
			$products = App::getModel('searchresults')->getProductDataset();
			$this->registry->template->assign('items', $products['rows']);
			$this->registry->template->assign('phrase', base64_encode($param));
			$result = $this->registry->template->fetch($this->loadTemplate('items.tpl'));
			App::getModel('searchresults')->addPhrase($param);
			echo $result;
			die();
		}
		else{
			echo '&nbsp;';
			die();
		}
	}
}
