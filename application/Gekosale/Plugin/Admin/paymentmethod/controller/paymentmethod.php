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
 * $Revision: 528 $
 * $Author: gekosale $
 * $Date: 2011-09-12 08:54:55 +0200 (Pn, 12 wrz 2011) $
 * $Id: paymentmethod.php 528 2011-09-12 06:54:55Z gekosale $ 
 */
namespace Gekosale\Plugin;

use FormEngine;

class PaymentmethodController extends Component\Controller\Admin
{

	public function index ()
	{
		App::getModel('contextmenu')->add($this->trans('TXT_INTEGRATION_PRZELEWY24'), $this->getRouter()->generate('admin', true, Array(
			'controller' => 'instancemanager',
			'action' => 'view',
			'param' => 'przelewy24'
		)));
		
		App::getModel('contextmenu')->add($this->trans('TXT_INTEGRATION_TRANSFERUJ'), $this->getRouter()->generate('admin', true, Array(
			'controller' => 'instancemanager',
			'action' => 'view',
			'param' => 'transferuj'
		)));
		
		App::getModel('contextmenu')->add($this->trans('TXT_SHIPPING_METHODS'), $this->getRouter()->url('admin', 'dispatchmethod'));
		
		$this->registry->xajax->registerFunction(array(
			'doDeletePaymentMethod',
			$this->model,
			'doAJAXDeletePaymentmethod'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllPaymentMethod',
			$this->model,
			'getPaymentmethodForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'disablePaymentmethod',
			$this->model,
			'doAJAXDisablePaymentmethod'
		));
		
		$this->registry->xajax->registerFunction(array(
			'enablePaymentmethod',
			$this->model,
			'doAJAXEnablePaymentmethod'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetNameSuggestions',
			$this->model,
			'getNameForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetControllerSuggestions',
			$this->model,
			'getControllerForAjax'
		));
		
		$this->registry->xajaxInterface->registerFunction(array(
			'doAJAXUpdateMethod',
			$this->model,
			'doAJAXUpdateMethod'
		));
		
		$this->renderLayout(array(
			'paymentmethod' => $this->model->getPaymentmethodAll(),
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'paymentmethod',
			'action' => '',
			'method' => 'post'
		));
		
		$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'required_data',
			'label' => $this->trans('TXT_MAIN_DATA')
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'name',
			'label' => $this->trans('TXT_NAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME')),
				new FormEngine\Rules\Unique($this->trans('ERR_NAME_ALREADY_EXISTS'), 'paymentmethod', 'name')
			)
		)));
		
		$Data = Event::dispatch($this, 'admin.paymentmethod.getPaymentMethods', Array(
			'data' => Array()
		));
		
		$requiredData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'controller',
			'label' => $this->trans('TXT_PAYMENT_CONTROLLER'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PAYMENT_CONTROLLER'))
			),
			'options' => FormEngine\Option::Make($Data)
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\MultiSelect(Array(
			'name' => 'dispatchmethod',
			'label' => $this->trans('TXT_DISPATCHMETHOD'),
			'options' => FormEngine\Option::Make(App::getModel('dispatchmethod')->getDispatchmethodToSelect())
		)));
		
		$layerData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'view_data',
			'label' => $this->trans('TXT_STORES')
		)));
		
		$layerData->AddChild(new FormEngine\Elements\LayerSelector(Array(
			'name' => 'view',
			'label' => $this->trans('TXT_VIEW')
		)));
		
		$populateData = Array(
			'required_data' => Array(
				'controller' => current(array_keys($Data))
			),
			'view_data' => Array(
				'view' => Helper::getViewIdsDefault()
			)
		);
		
		$form->Populate($populateData);
		
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\NoCode());
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$id = $this->model->addNewPaymentmethod($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/paymentmethod/add');
			}
			else{
				if (Helper::getViewId() > 0){
					App::getContainer()->get('session')->setVolatileMessage("Moduł płatności został dodany. Skonfiguruj go teraz w zakładce Konfiguracja.");
					App::redirect(__ADMINPANE__ . '/paymentmethod/edit/' . $id);
				}
				else{
					App::redirect(__ADMINPANE__ . '/paymentmethod');
				}
			}
		}
		
		$this->registry->template->assign('form', $form->Render());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->display($this->loadTemplate('add.tpl'));
	}

	public function edit ()
	{
		$paymentMethodModel = $this->model->getPaymentmethodModelById($this->registry->core->getParam());
		
		if (empty($paymentMethodModel)){
			App::redirect(__ADMINPANE__ . '/paymentmethod');
		}
		
		$rawPaymentmethodData = $this->model->getPaymentmethodView($this->id);
		
		$paymentmethodData = Array(
			'paymentmethodmodel' => $paymentMethodModel,
			'required_data' => Array(
				'name' => $rawPaymentmethodData['name'],
				'dispatchmethod' => $rawPaymentmethodData['dispatchmethod']
			),
			'view_data' => Array(
				'view' => $rawPaymentmethodData['view']
			)
		);
		
		$this->formModel->setPopulateData($paymentmethodData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editPaymentmethod($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->registry->core->getParam(), $paymentMethodModel);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/paymentmethod');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}