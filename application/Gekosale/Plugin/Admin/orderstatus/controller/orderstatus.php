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
 * $Id: orderstatus.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Plugin;
use FormEngine;

class OrderStatusController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doDeleteOrderstatus',
			$this->model,
			'doAJAXDeleteOrderstatus'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllOrderstatus',
			$this->model,
			'getOrderstatusForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetNameSuggestions',
			$this->model,
			'getNameForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'setDefault',
			$this->model,
			'doAJAXDefault'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->addNewOrderstatus($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/orderstatus/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/orderstatus');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		
		$rawOrderstatusData = $this->model->getOrderstatusView($this->id);
		
		$populateData = Array(
			'required_data' => Array(
				'language_data' => $rawOrderstatusData['language'],
				'orderstatusgroupsid' => $rawOrderstatusData['orderstatusgroupsid']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editOrderstatus($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->registry->core->getParam());
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/orderstatus');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}