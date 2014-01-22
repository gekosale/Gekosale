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
 * $Id: upsell.php 576 2011-10-22 08:23:55Z gekosale $ 
 */

namespace Gekosale\Plugin;
use FormEngine;

class UpsellController extends Component\Controller\Admin
{

	public function index ()
	{
        App::getModel('contextmenu')->add($this->trans('TXT_BUYALSO_STATS'), $this->getRouter()->url('admin', 'buyalso'));
        App::getModel('contextmenu')->add($this->trans('TXT_PRODUCT_STATS'), $this->getRouter()->url('admin', 'statsproducts'));
        App::getModel('contextmenu')->add($this->trans('TXT_SALES_STATS'), $this->getRouter()->url('admin', 'statssales'));
        App::getModel('contextmenu')->add($this->trans('TXT_PRODUCTS'), $this->getRouter()->url('admin', 'product'));

        
		$this->registry->xajaxInterface->registerFunction(array(
			'LoadCategoryChildren',
			App::getModel('product'),
			'loadCategoryChildren'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllUpsell',
			$this->model,
			'getUpsellForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetNameSuggestions',
			$this->model,
			'getNameForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'doDeleteUpsell',
			$this->model,
			'doAJAXDeleteUpsell'
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
				App::redirect(__ADMINPANE__ . '/upsell/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/upsell');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		
		$upsell = $this->model->getUpsellView($this->id);
		
		$populateData = Array(
			'required_data' => Array(
				'name' => $upsell['name']
			),
			'related_products' => Array(
				'products' => $this->model->getProductsDataGrid($this->id)
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			
			$this->model->editRelated($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			App::redirect(__ADMINPANE__ . '/upsell');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}