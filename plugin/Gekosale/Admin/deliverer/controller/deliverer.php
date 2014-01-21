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
 * $Id: deliverer.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;
use FormEngine;

class DelivererController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doDeleteDeliverer',
			$this->model,
			'doAJAXDeleteDeliverer'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllDeliverer',
			$this->model,
			'getDelivererForAjax'
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
			$this->model->addNewDeliverer($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/deliverer/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/deliverer');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		
		$rawDelivererData = $this->model->getDelivererView($this->id);
		
		if (empty($rawDelivererData)){
			App::redirect(__ADMINPANE__ . '/deliverer');
		}
		
		$populateData = Array(
			'required_data' => Array(
				'language_data' => $rawDelivererData['language']
			),
			'related_products' => Array(
				'products' => $this->model->getProductsForDelilverer((int) $this->id)
			),
			'photos_pane' => Array(
				'photo' => $rawDelivererData['photo']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editDeliverer($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/deliverer');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}