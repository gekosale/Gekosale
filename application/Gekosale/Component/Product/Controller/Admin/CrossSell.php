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
 * $Id: crosssell.php 576 2011-10-22 08:23:55Z gekosale $ 
 */

namespace Gekosale\Component\Crosssell\Controller\Admin;
use Gekosale\Core\Component\Controller\Admin;
use FormEngine;

class CrossSell extends Admin
{

	public function index ()
	{
        
        App::getModel('contextmenu')->add($this->trans('TXT_BUYALSO_STATS'), $this->getRouter()->url('admin', 'buyalso'));
        App::getModel('contextmenu')->add($this->trans('TXT_PRODUCT_STATS'), $this->getRouter()->url('admin', 'statsproducts'));

        
        
		$this->registry->xajaxInterface->registerFunction(array(
			'LoadCategoryChildren',
			App::getModel('product'),
			'loadCategoryChildren'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllCrosssell',
			$this->model,
			'getCrosssellForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetNameSuggestions',
			$this->model,
			'getNameForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'doDeleteCrosssell',
			$this->model,
			'doAJAXDeleteCrosssell'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->addNewRelated($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/crosssell/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/crosssell');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		$crosssell = $this->model->getCrossSellView((int) $this->id);
		
		$populateData = Array(
			'required_data' => Array(
				'name' => $crosssell['name']
			),
			'related_products' => Array(
				'products' => $this->model->getProductsDataGrid((int) $this->id)
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->editRelated($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			App::redirect(__ADMINPANE__ . '/crosssell');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}