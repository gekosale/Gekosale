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
 * $Revision: 464 $
 * $Author: gekosale $
 * $Date: 2011-08-31 08:19:48 +0200 (Śr, 31 sie 2011) $
 * $Id: buyalso.php 464 2011-08-31 06:19:48Z gekosale $ 
 */
namespace Gekosale;

use FormEngine;

class BuyAlsoController extends Component\Controller\Admin
{

	public function index ()
	{
		App::getModel('contextmenu')->add($this->trans('TXT_SALES_STATS'), $this->getRouter()->url('admin', 'statssales'));
		App::getModel('contextmenu')->add($this->trans('TXT_PRODUCTS'), $this->getRouter()->url('admin', 'product'));
		App::getModel('contextmenu')->add($this->trans('TXT_PRODUCT_PROMOTIONS'), $this->getRouter()->url('admin', 'productpromotion'));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllBuyalso',
			$this->model,
			'getBuyalsoForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetNameSuggestions',
			$this->model,
			'getNameForAjax'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function view ()
	{
		$name = App::getModel('product')->getProductTranslation((int) $this->registry->core->getParam());
		
		$this->renderLayout(Array(
			'id' => $this->id,
			'name' => isset($name[Helper::getLanguageId()]['name']) ? $name[Helper::getLanguageId()]['name'] : ''
		));
	}

	public function confirm ()
	{
		echo $this->model->alsoChart((int) $this->registry->core->getParam());
	}
}