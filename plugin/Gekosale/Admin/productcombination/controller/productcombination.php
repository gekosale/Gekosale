<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms 
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the 
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace Gekosale;

use FormEngine;

class ProductCombinationController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doDeleteCombination',
			App::getModel('productcombination'),
			'doAJAXDeleteProductCombination'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllCombination',
			App::getModel('productcombination'),
			'getCombinationForAjax'
		));
		
		$this->renderLayout(array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			App::getModel('productcombination')->addNewCombination($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/productcombination/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/productcombination');
			}
		}
		
		$this->renderLayout(array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		$rawProductCombinationData = App::getModel('productcombination')->getCombinationView($this->registry->core->getParam());
		
		$productCombinationData = Array(
			'related_products' => Array(
				'products' => $rawProductCombinationData['products']
			),
			'price_pane' => Array(
				'discount' => $rawProductCombinationData['value']
			),
			'view_data' => Array(
				'view' => $rawProductCombinationData['view']
			)
		);
		
		$this->formModel->setPopulateData($productCombinationData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				App::getModel('productcombination')->editCombination($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->registry->core->getParam());
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/productcombination');
		}
		
		$this->renderLayout(array(
			'form' => $form->Render()
		));
	}
}