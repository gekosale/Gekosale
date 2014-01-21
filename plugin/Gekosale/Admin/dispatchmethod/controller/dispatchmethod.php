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
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: dispatchmethod.php 619 2011-12-19 21:09:00Z gekosale $ 
 */
namespace Gekosale;

use FormEngine;

class DispatchmethodController extends Component\Controller\Admin
{

	public function index ()
	{
		/*App::getModel('contextmenu')->add($this->trans('TXT_INTEGRATION_KURJERZY'), $this->getRouter()->generate('admin', true, Array(
			'controller' => 'instancemanager',
			'action' => 'view',
			'param' => 'kurjerzy'
		)));
		
		App::getModel('contextmenu')->add($this->trans('TXT_INTEGRATION_FURGONETKA'), $this->getRouter()->generate('admin', true, Array(
			'controller' => 'instancemanager',
			'action' => 'view',
			'param' => 'furgonetka'
		)));
		
		App::getModel('contextmenu')->add($this->trans('TXT_INTEGRATION_SENDIT'), $this->getRouter()->generate('admin', true, Array(
			'controller' => 'instancemanager',
			'action' => 'view',
			'param' => 'sendit'
		)));*/
		
		App::getModel('contextmenu')->add($this->trans('TXT_PAYMENT_METHODS'), $this->getRouter()->url('admin', 'paymentmethod'));
		
		$this->registry->xajax->registerFunction(array(
			'doDeleteDispatchMethod',
			$this->model,
			'doAJAXDeleteDispatchmethod'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllDispatchMethod',
			$this->model,
			'getDispatchmethodForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetNameSuggestions',
			$this->model,
			'getNameForAjax'
		));
		
		$this->registry->xajaxInterface->registerFunction(array(
			'doAJAXUpdateMethod',
			$this->model,
			'doAJAXUpdateMethod'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->addNewDispatchmethod($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/dispatchmethod/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/dispatchmethod');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		$rawDispatchmethodData = $this->model->getDispatchmethodView($this->registry->core->getParam());
		
		if (empty($rawDispatchmethodData)){
			App::redirect(__ADMINPANE__ . '/dispatchmethod');
		}
		
		$populateData = Array(
			'required_data' => Array(
				'name' => $rawDispatchmethodData['name'],
				'paymentmethodname' => $rawDispatchmethodData['paymentmethods'],
				'type' => $rawDispatchmethodData['type'],
				'currencyid' => $rawDispatchmethodData['currencyid']
			),
			'dispatchmethod_data' => Array(
				'table' => $this->model->getDispatchmethodPrice((int) $this->registry->core->getParam()),
				'maximumweight' => $rawDispatchmethodData['maximumweight']
			),
			'dispatchmethodweight_data' => Array(
				'tableweight' => $this->model->getDispatchmethodWeight((int) $this->registry->core->getParam()),
				'freedelivery' => $rawDispatchmethodData['freedelivery']
			),
			'description_data' => Array(
				'description' => $rawDispatchmethodData['description']
			),
			'photos_pane' => Array(
				'photo' => $rawDispatchmethodData['photo']
			),
			'view_data' => Array(
				'view' => $rawDispatchmethodData['view']
			),
			'country_pane' => Array(
				'countryids' => $rawDispatchmethodData['countryids']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editDispatchmethod($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->registry->core->getParam());
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/dispatchmethod');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}