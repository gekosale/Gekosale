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
 * $Revision: 552 $
 * $Author: gekosale $
 * $Date: 2011-10-08 17:56:59 +0200 (So, 08 paź 2011) $
 * $Id: producer.php 552 2011-10-08 15:56:59Z gekosale $ 
 */

namespace Gekosale\Plugin;
use FormEngine;

class ProducerController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doDeleteProducer',
			$this->model,
			'doAJAXDeleteProducer'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllProducer',
			$this->model,
			'getDataForAjax'
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
			
			try{
				$this->model->save($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/producer/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/producer');
			}
		}
		
		$this->registry->xajaxInterface->registerFunction(array(
			'doAJAXCreateSeo',
			App::getModel('seo'),
			'doAJAXCreateSeo'
		));
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		$populateData = $this->model->getDataById($this->id);
		
		if (empty($populateData)){
			App::redirect(__ADMINPANE__ . '/producer');
		}
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			
			try{
				$this->model->save($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			
			App::redirect(__ADMINPANE__ . '/producer');
		}
		
		$this->registry->xajaxInterface->registerFunction(array(
			'doAJAXCreateSeo',
			App::getModel('seo'),
			'doAJAXCreateSeo'
		));
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}