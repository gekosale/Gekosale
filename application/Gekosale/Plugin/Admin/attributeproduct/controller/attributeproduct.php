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
 * $Id: attributeproduct.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Plugin;
use FormEngine;

class AttributeProductController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doDeleteAttributeProducts',
			$this->model,
			'doAJAXDeleteAttributeProducts'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllAttributeProducts',
			$this->model,
			'getAttributeProductsForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetNameSuggestions',
			$this->model,
			'getNameForAjax'
		));
		
		$this->renderLayout();
	}

	public function add ()
	{
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->addAttributeGroup($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/attributeproduct/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/attributeproduct');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		
		$rawAttributeproductData = $this->model->getAttributeProductName($this->registry->core->getParam());
		
		$populateData = Array(
			'required_data' => Array(
				'attributeproductname' => $rawAttributeproductData['attributeproductname']
			),
			'attributes_data' => Array(
				'attributeproductvalues' => $rawAttributeproductData['attributes']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->updateAttribute($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->registry->core->getParam());
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/attributeproduct');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}