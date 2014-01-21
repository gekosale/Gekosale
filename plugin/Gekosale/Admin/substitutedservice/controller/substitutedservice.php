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
 * $Id: substitutedservice.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;
use FormEngine;

class SubstitutedServiceController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doDeleteSubstitutedservice',
			$this->model,
			'doAJAXDeleteSubstitutedservice'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllSubstitutedservice',
			$this->model,
			'getSubstitutedserviceForAjax'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		
		$populateData = Array(
			'main_data' => Array(
				'actionid' => Array(
					'value' => '1'
				)
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->addSubstitutedService($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/substitutedservice/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/substitutedservice');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		
		$rawNotificationEdit = $this->model->getSubstitutedServiceToEdit($this->id);
		
		if (is_array($rawNotificationEdit) && ! empty($rawNotificationEdit)){
			$populateData = Array(
				'main_data' => Array(
					'name' => $rawNotificationEdit['name'],
					'admin' => $rawNotificationEdit['admin']
				)
			);
			if ($rawNotificationEdit['actionid'] == 2){
				$populateData['main_data']['actionid'] = Array(
					'value' => $rawNotificationEdit['actionid'],
					$rawNotificationEdit['actionid'] => $rawNotificationEdit['date']
				);
			}
			else{
				$populateData['main_data']['actionid'] = Array(
					'value' => $rawNotificationEdit['actionid'],
					$rawNotificationEdit['actionid'] => $rawNotificationEdit['periodid']
				);
			}
		
		}
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->editSubstitutedService($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			App::redirect(__ADMINPANE__ . '/substitutedservice');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function confirm ()
	{
		
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'confirm_substitutedservicesend',
			'action' => '',
			'method' => 'post'
		));
		
		$clientData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'client_data',
			'label' => $this->trans('TXT_CLIENTS_LIST')
		)));
		
		$clientData->AddChild(new FormEngine\Elements\DatagridSelect(Array(
			'name' => 'clients',
			'label' => $this->trans('TXT_CLIENT'),
			'key' => 'idclient',
			'datagrid_init_function' => Array(
				App::getModel('client'),
				'initDatagrid'
			),
			'repeat_max' => FormEngine\FE::INFINITE,
			'columns' => $this->getClientDatagridColumns()
		)));
		
		$clientsArrayRaw = $this->model->getClientsForSubstitutedServicesSend((int) $this->registry->core->getParam());
		
		$clients = Array();
		if (! empty($clientsArrayRaw)){
			foreach ($clientsArrayRaw as $client){
				array_push($clients, $client['idclient']);
			}
			$clients = Array(
				'client_data' => Array(
					'clients' => $clients
				)
			);
			$form->Populate($clients);
		}
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$send = $this->model->saveSendingInfoNotification($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), (int) $this->registry->core->getParam());
			if ($send > 0){
				App::redirect(__ADMINPANE__ . '/substitutedservice/view/' . (int) $this->registry->core->getParam());
			}
			else{
				App::redirect(__ADMINPANE__ . '/substitutedservicesend/confirm/' . (int) $this->registry->core->getParam());
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function view ()
	{
		
		App::getContainer()->get('session')->setActiveQuequeParam((int) $this->registry->core->getParam());
		
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'add_substitutedservicesend',
			'action' => '',
			'method' => 'post'
		));
		
		$progress = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'progres_data',
			'label' => $this->trans('TXT_SENDING')
		)));
		
		$progress->AddChild(new FormEngine\Elements\ProgressIndicator(Array(
			'name' => 'progress',
			'label' => $this->trans('TXT_PROGRESS'),
			'chunks' => 30,
			'load' => Array(
				$this->model,
				'doLoadQueque'
			),
			'process' => Array(
				$this->model,
				'doProcessQueque'
			),
			'success' => Array(
				$this->model,
				'doSuccessQueque'
			),
			'preventSubmit' => true
		)));
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	protected function getClientDatagridColumns ()
	{
		return Array(
			Array(
				'id' => 'idclient',
				'caption' => $this->trans('TXT_ID'),
				'appearance' => Array(
					'width' => 50
				)
			),
			Array(
				'id' => 'firstname',
				'caption' => $this->trans('TXT_FIRSTNAME'),
				'appearance' => Array(
					'width' => FormEngine\Elements\DatagridSelect::WIDTH_AUTO
				)
			),
			Array(
				'id' => 'surname',
				'caption' => $this->trans('TXT_SURNAME'),
				'appearance' => Array(
					'width' => FormEngine\Elements\DatagridSelect::WIDTH_AUTO
				)
			),
			Array(
				'id' => 'groupname',
				'caption' => $this->trans('TXT_GROUPS_CLIENT'),
				'appearance' => Array(
					'width' => FormEngine\Elements\DatagridSelect::WIDTH_AUTO
				)
			),
			Array(
				'id' => 'adddate',
				'caption' => $this->trans('TXT_DATE'),
				'appearance' => Array(
					'width' => FormEngine\Elements\DatagridSelect::WIDTH_AUTO
				)
			)
		);
	}
}