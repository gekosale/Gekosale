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
 * $Id: contact.php 464 2011-08-31 06:19:48Z gekosale $ 
 */

namespace Gekosale\Plugin;
use FormEngine;

class contactController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doDeleteContact',
			$this->model,
			'doAJAXDeleteContact'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllContact',
			$this->model,
			'getContactForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetNameSuggestions',
			$this->model,
			'getNameForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetStreetSuggestions',
			$this->model,
			'getStreetForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetPlacenameSuggestions',
			$this->model,
			'getPlacenameForAjax'
		));
		
		$this->renderLayout();
	}

	public function add ()
	{
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->addNewContact($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/contact/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/contact');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		
		$rawData = $this->model->getContactView($this->registry->core->getParam());
		
		if(empty($rawData)){
			App::redirect(__ADMINPANE__ . '/contact');
		}
		
		$populateData = Array(
			'required_data' => Array(
				'language_data' => $rawData['language'],
				'publish' => $rawData['publish']
			),
			'address_data' => Array(
				'language_data' => $rawData['language']
			),
			'view_data' => Array(
				'view' => $rawData['view']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editContact($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->registry->core->getParam());
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/contact');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}