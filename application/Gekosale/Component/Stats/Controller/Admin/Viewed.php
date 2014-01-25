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
 * $Id: mostviewed.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Component\Mostviewed\Controller\Admin;
use Gekosale\Core\Component\Controller\Admin;

class Viewed extends Admin
{

	public function index ()
	{
        
        App::getModel('contextmenu')->add($this->trans('TXT_SALES_STATS'), $this->getRouter()->url('admin', 'statssales'));
        App::getModel('contextmenu')->add($this->trans('TXT_PRODUCTS'), $this->getRouter()->url('admin', 'product'));
        App::getModel('contextmenu')->add($this->trans('TXT_PRODUCT_PROMOTIONS'), $this->getRouter()->url('admin', 'productpromotion'));
        App::getModel('contextmenu')->add($this->trans('TXT_BUYALSO_STATS'), $this->getRouter()->url('admin', 'buyalso'));

        
		$this->registry->xajaxInterface->registerFunction(array(
			'LoadCategoryChildren',
			App::getModel('product'),
			'loadCategoryChildren'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllMostViewed',
			$this->model,
			'getMostViewedForAjax'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function delete ()
	{
		$this->model->deleteMostViewed();
		App::redirect(__ADMINPANE__ . '/mostviewed');
	}
}