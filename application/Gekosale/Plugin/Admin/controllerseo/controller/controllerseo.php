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
 * $Id: controllerseo.php 464 2011-08-31 06:19:48Z gekosale $ 
 */

namespace Gekosale\Plugin;
use FormEngine;

class ControllerSeoController extends Component\Controller\Admin
{

	public function __construct ($registry)
	{
		parent::__construct($registry);
		$this->model = App::getModel('controllerseo');
		$this->formModel = App::getFormModel('controllerseo');
	}
	
	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doUpdateControllerSeo',
			$this->model,
			'doAJAXUpdateControllerSeo'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllControllerSeo',
			$this->model,
			'getControllerSeoForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetTranslationSuggestions',
			$this->model,
			'getTranslationNameForAjax'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function edit ()
	{
		$rawControllerSeoData = $this->model->getControllerSeoView($this->id);
		
		if (empty($rawControllerSeoData)){
			App::redirect(__ADMINPANE__ . '/controllerseo');
		}
		
		$populateData = Array(
			'required_data' => Array(
				'controller' => $rawControllerSeoData['name'],
				'language_data' => $rawControllerSeoData['translation']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->updateControllerSeo($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/controllerseo');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}