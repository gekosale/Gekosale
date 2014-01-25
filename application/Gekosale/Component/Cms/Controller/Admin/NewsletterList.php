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
 * $Id: recipientlist.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Component\Recipientlist\Controller\Admin;
use Gekosale\Core\Component\Controller\Admin;
use FormEngine;

class NewsletterList extends Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doDeleteRecipientList',
			$this->model,
			'doAJAXDeleteRecipientList'
		));
		$this->registry->xajax->registerFunction(array(
			'LoadAllRecipientList',
			$this->model,
			'getRecipientListForAjax'
		));
		$this->renderLayout(array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'recipientlist',
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
				new FormEngine\Rules\Unique($this->trans('ERR_NAME_ALREADY_EXISTS'), 'recipientlist', 'name')
			)
		)));
		
		$clientgroupData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'clientgroup_data',
			'label' => $this->trans('TXT_CLIENT_GROUPS_LIST')
		)));
		
		$clientgroupData->AddChild(new FormEngine\Elements\DatagridSelect(Array(
			'name' => 'clientgroup',
			'label' => $this->trans('TXT_CLIENTGROUPS'),
			'key' => 'idclientgroup',
			'datagrid_init_function' => Array(
				App::getModel('clientgroup'),
				'initDatagrid'
			),
			'repeat_max' => FormEngine\FE::INFINITE,
			'columns' => $this->getClientGroupDatagridColumns()
		)));
		
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
		
		$clientnewsletterData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'clientnewsletter_data',
			'label' => $this->trans('TXT_CLIENT_NEWSLETTER_LIST')
		)));
		
		$clientnewsletterData->AddChild(new FormEngine\Elements\DatagridSelect(Array(
			'name' => 'clientnewsletter',
			'label' => $this->trans('TXT_CLIENT_NEWSLETTER'),
			'key' => 'idclientnewsletter',
			'datagrid_init_function' => Array(
				App::getModel('clientnewsletter'),
				'initDatagrid'
			),
			'repeat_max' => FormEngine\FE::INFINITE,
			'columns' => $this->getClientNewsletterDatagridColumns()
		)));
		
		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->addNewRecipient($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/recipientlist/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/recipientlist');
			}
		}
		
		$this->registry->template->assign('form', $form->Render());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->display($this->loadTemplate('add.tpl'));
	}

	public function edit ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'recipientlist',
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
				new FormEngine\Rules\Unique($this->trans('ERR_NAME_ALREADY_EXISTS'), 'recipientlist', 'name', null, Array(
					'column' => 'idrecipientlist',
					'values' => (int) $this->registry->core->getParam()
				))
			)
		)));
		
		$clientgroupData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'clientgroup_data',
			'label' => $this->trans('TXT_CLIENT_GROUPS_LIST')
		)));
		
		$clientgroupData->AddChild(new FormEngine\Elements\DatagridSelect(Array(
			'name' => 'clientgroup',
			'label' => $this->trans('TXT_CLIENTGROUPS'),
			'key' => 'idclientgroup',
			'datagrid_init_function' => Array(
				App::getModel('clientgroup'),
				'initDatagrid'
			),
			'repeat_max' => FormEngine\FE::INFINITE,
			'columns' => $this->getClientGroupDatagridColumns()
		)));
		
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
		
		$clientnewsletterData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'clientnewsletter_data',
			'label' => $this->trans('TXT_CLIENT_NEWSLETTER_LIST')
		)));
		
		$clientnewsletterData->AddChild(new FormEngine\Elements\DatagridSelect(Array(
			'name' => 'clientnewsletter',
			'label' => $this->trans('TXT_CLIENT_NEWSLETTER'),
			'key' => 'idclientnewsletter',
			'datagrid_init_function' => Array(
				App::getModel('clientnewsletter'),
				'initDatagrid'
			),
			'repeat_max' => FormEngine\FE::INFINITE,
			'columns' => $this->getClientNewsletterDatagridColumns()
		)));
		
		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		$rawRecipientListData = $this->model->getRecipientListView($this->registry->core->getParam());
		
		$groups = Array();
		foreach ($rawRecipientListData['clientgrouplist'] as $groupKey => $groupValue){
			array_push($groups, $groupValue['clientgroupid']);
		}
		
		$clients = Array();
		foreach ($rawRecipientListData['clientlist'] as $clientKey => $clientValue){
			array_push($clients, $clientValue['clientid']);
		}
		
		$clientnewsletter = Array();
		foreach ($rawRecipientListData['clientnewsletterlist'] as $clientnewsletterKey => $clientnewsletterValue){
			array_push($clientnewsletter, $clientnewsletterValue['clientnewsletterid']);
		}
		$RecipientListData = Array(
			'required_data' => Array(
				'name' => $rawRecipientListData['name']
			),
			'clientgroup_data' => Array(
				'clientgroup' => $groups
			),
			'client_data' => Array(
				'clients' => $clients
			),
			'clientnewsletter_data' => Array(
				'clientnewsletter' => $clientnewsletter
			)
		);
		
		$form->Populate($RecipientListData);
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editRecipientList($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->registry->core->getParam());
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/recipientlist');
		}
		
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->assign('form', $form->Render());
		$this->registry->template->display($this->loadTemplate('edit.tpl'));
	}

	protected function getClientGroupDatagridColumns ()
	{
		return Array(
			Array(
				'id' => 'idclientgroup',
				'caption' => $this->trans('TXT_ID'),
				'appearance' => Array(
					'width' => 50
				)
			),
			Array(
				'id' => 'name',
				'caption' => $this->trans('TXT_NAME'),
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

	protected function getClientNewsletterDatagridColumns ()
	{
		return Array(
			Array(
				'id' => 'idclientnewsletter',
				'caption' => $this->trans('TXT_ID'),
				'appearance' => Array(
					'width' => 50
				)
			),
			Array(
				'id' => 'email',
				'caption' => $this->trans('TXT_EMAIL'),
				'appearance' => Array(
					'width' => FormEngine\Elements\DatagridSelect::WIDTH_AUTO
				)
			),
			Array(
				'id' => 'active',
				'caption' => $this->trans('TXT_ACTIVE'),
				'appearance' => Array(
					'width' => FormEngine\Elements\DatagridSelect::WIDTH_AUTO
				)
			)
		);
	}
}