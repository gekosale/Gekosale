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
 * $Revision: 583 $
 * $Author: gekosale $
 * $Date: 2011-10-28 22:19:07 +0200 (Pt, 28 paź 2011) $
 * $Id: client.php 583 2011-10-28 20:19:07Z gekosale $ 
 */

namespace Gekosale\Component\Client\Controller\Admin;
use Gekosale\Core\Component\Controller\Admin;
use FormEngine;

class Client extends Admin
{

	public function index ()
	{
        
        App::getModel('contextmenu')->add($this->trans('TXT_ORDERS'), $this->getRouter()->url('admin', 'order'));
        App::getModel('contextmenu')->add($this->trans('TXT_CLIENTGROUP'), $this->getRouter()->url('admin', 'clientgroup'));

        
		$this->registry->xajax->registerFunction(array(
			'doDeleteClient',
			$this->model,
			'doAJAXDeleteClient'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllClient',
			$this->model,
			'getClientForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetFirstnameSuggestions',
			$this->model,
			'getFirstnameForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetSurnameSuggestions',
			$this->model,
			'getSurnameForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'disableClient',
			$this->model,
			'doAJAXDisableClient'
		));
		
		$this->registry->xajax->registerFunction(array(
			'enableClient',
			$this->model,
			'doAJAXEnableClient'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		$this->registry->xajaxInterface->registerFunction(Array(
			'AddClientGroup',
			App::getModel('clientgroup'),
			'addEmptyClientGroup'
		));
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$generatedPassword = Core::passwordGenerate();
			$clientId = $this->model->addNewClient($form->getSubmitValues(), $generatedPassword);
			$Data = $form->getSubmitValues();
			$Data['personal_data']['password'] = $generatedPassword;
			$this->registry->template->assign('personal_data', $Data['personal_data']);
			$this->registry->template->assign('address', $Data['billing_data']);
			
			App::getModel('mailer')->sendEmail(Array(
				'template' => 'addClientFromAdmin',
				'email' => Array(
					$Data['personal_data']['email']
				),
				'bcc' => false,
				'subject' => $this->trans('TXT_REGISTRATION_NEW'),
				'viewid' => $Data['personal_data']['viewid']
			));
			
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/client/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/client');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		$this->registry->xajaxInterface->registerFunction(Array(
			'AddClientGroup',
			App::getModel('clientgroup'),
			'addEmptyClientGroup'
		));
		
		$rawClientData = $this->model->getClientView($this->id);
		
		$populateData = Array(
			'personal_data' => Array(
				'viewid' => $rawClientData['viewid'],
				'firstname' => $rawClientData['firstname'],
				'surname' => $rawClientData['surname'],
				'email' => $rawClientData['email'],
				'newsletter' => $rawClientData['newsletter'],
				'phone' => $rawClientData['phone'],
				'phone2' => $rawClientData['phone2'],
				'clientgroupid' => $rawClientData['clientgroupid'],
				'autoassign' => $rawClientData['autoassign'],
			),
			'billing_data' => Array(
				'firstname' => $rawClientData['billing_address']['firstname'],
				'surname' => $rawClientData['billing_address']['surname'],
				'street' => $rawClientData['billing_address']['street'],
				'streetno' => $rawClientData['billing_address']['streetno'],
				'placeno' => $rawClientData['billing_address']['placeno'],
				'placename' => $rawClientData['billing_address']['placename'],
				'postcode' => $rawClientData['billing_address']['postcode'],
				'companyname' => $rawClientData['billing_address']['companyname'],
				'nip' => $rawClientData['billing_address']['nip'],
				'countryid' => $rawClientData['billing_address']['countryid']
			),
			'shipping_data' => Array(
				'firstname' => $rawClientData['delivery_address']['firstname'],
				'surname' => $rawClientData['delivery_address']['surname'],
				'street' => $rawClientData['delivery_address']['street'],
				'streetno' => $rawClientData['delivery_address']['streetno'],
				'placeno' => $rawClientData['delivery_address']['placeno'],
				'placename' => $rawClientData['delivery_address']['placename'],
				'postcode' => $rawClientData['delivery_address']['postcode'],
				'companyname' => $rawClientData['delivery_address']['companyname'],
				'nip' => $rawClientData['delivery_address']['nip'],
				'countryid' => $rawClientData['delivery_address']['countryid']
			),
			'additional_data' => Array(
				'description' => $rawClientData['description'],
				'disable' => $rawClientData['disable']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editClient($form->getSubmitValues(), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/client');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

}