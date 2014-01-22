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
 * $Id: substitutedservicesend.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Plugin;
use FormEngine;
use sfEvent;

class substitutedservicesendController extends Component\Controller\Admin
{

	public function __construct ($registry)
	{
		parent::__construct($registry);
		$this->model = App::getModel('substitutedservicesend');
	}

	public function index ()
	{
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllSubstitutedservice',
			$this->model,
			'getSubstitutedserviceForAjax'
		));
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->assign('datagrid_filter', $this->model->getDatagridFilterData());
		$this->registry->template->display($this->loadTemplate('index.tpl'));
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
				App::redirect(__ADMINPANE__ . '/substitutedservicesend/add/' . $send);
			}
			else{
				App::redirect(__ADMINPANE__ . '/substitutedservicesend/confirm/' . (int) $this->registry->core->getParam());
			}
		}
		$this->registry->template->assign('form', $form->Render());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->display($this->loadTemplate('confirm.tpl'));
	}

	public function add ()
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
		
		$progress->AddChild(new FE_ProgressIndicator(Array(
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
			)
		)));
		
		$this->registry->template->assign('form', $form->Render());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->display($this->loadTemplate('add.tpl'));
	}

	public function view ()
	{
		
		$substitutedserviceid = $this->registry->core->getParam();
		
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'view_substitutedservicesend',
			'action' => '',
			'method' => 'post'
		));
		
		$listNotifications = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'substitutedservicesend',
			'label' => $this->trans('TXT_NOTIFICATIONS_REPORT')
		)));
		
		$list = $listNotifications->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'list',
			'label' => $this->trans('TXT_CHOOSE_NOTIFICATION_DATE_FOR_REPORT'),
			'options' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + $this->model->getNotificationstAllToSelect($substitutedserviceid))
		)));
		
		$listNotifications->AddChild(new FormEngine\Elements\StaticText(Array(
			'text' => '<p id="link" />'
		)));
		
		$listNotifications->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p align="center"><strong>' . $this->trans('TXT_ATTENTION') . '!!!</strong></p>
					 <p> Wysłanie powiadomienia spowoduje przesłanie informacji tylko do tych
						klientów, którzy dla określonego powiadomienia
						posiadają status "Wiadomość nie została jeszcze wysłana".
					 </p>',
			'direction' => FormEngine\Elements\Tip::UP,
			'short_tip' => '<p><strong>' . $this->trans('TXT_ATTENTION') . '!!!</strong></p>',
			'dependencies' => Array(
				new FormEngine\Dependency( FormEngine\Dependency::HIDE, $list, new FormEngine\Conditions\Equals(0))
			)
		)));
		
		$clients = $listNotifications->AddChild(new FormEngine\Elements\StaticListing(Array(
			'name' => 'clients',
			'title' => '',
			'values' => Array(
				new FormEngine\ListItem('', '')
			)
		)));
		App::getRegistry()->xajaxInterface->registerFunction(array(
			'GetAllClientsForNotification',
			$this->model,
			'GetAllClientsForNotification'
		));
		$clients->AddDependency(new FormEngine\Dependency( FormEngine\Dependency::INVOKE_CUSTOM_FUNCTION, $list, 'ChangeClientsListForNotification'));
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			App::redirect(__ADMINPANE__ . '/substitutedservicesend/index');
		}
		
		$this->registry->template->assign('form', $form->Render());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->display($this->loadTemplate('view.tpl'));
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