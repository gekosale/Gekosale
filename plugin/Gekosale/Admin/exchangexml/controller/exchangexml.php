<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o
 * licencji i autorach.
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
 * $Id: product.php 619 2011-12-19 21:09:00Z gekosale $
 */
namespace Gekosale;

use FormEngine;

class ExchangexmlController extends Component\Controller\Admin
{
	public function __construct ($registry)
	{
		parent::__construct($registry);

		$this->registry->xajaxInterface->registerFunction(array(
			'doAJAXgetProfile',
			$this->model,
			'doAJAXgetProfile'
		));
	}

	public function index () {
		$this->registry->xajax->registerFunction(array(
			'doDeleteOperation',
			$this->model,
			'doAJAXDeleteOperation'
		));

		$this->registry->xajax->registerFunction(array(
			'LoadAllOperations',
			$this->model,
			'getOperationsForAjax'
		));

		$this->renderLayout(array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		$form = $this->formModel->initForm();

		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$Data = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);

			if ($this->model->addOperation($Data)) {
				App::getContainer()->get('session')->setVolatileMessage('Nowa operacja została dodana');
			}
			else {
				App::getContainer()->get('session')->setVolatileMessage('Wystąpił problem podczas dodawania nowej operacji');
			}
			App::redirect(__ADMINPANE__ . '/exchangexml/index');
		}
		else{

			$this->renderLayout(array(
				'form' => $form->Render()
			));
		}
	}

	public function edit ()
	{
		$PopulateData = $this->model->getOperationById((int) $this->id);

		if (empty($PopulateData)) {
			App::redirect(__ADMINPANE__ . '/exchangexml/index');
		}

		$Data = array(
			'profile_pane' => array(
				'profile_name' => $PopulateData['name'],
				'profile_type' => $PopulateData['type'],
				'profile_datatype' => $PopulateData['datatype'],
				'profile_pattern' => $PopulateData['pattern'],
				'profile_categoryseparator' => $PopulateData['categoryseparator'],
				'remote_pane' => array(
					'profile_url' => $PopulateData['url'],
					'profile_username' => $PopulateData['username'],
					'profile_password' => $PopulateData['password'],
				),
				'profile_periodically' => $PopulateData['periodically'],
				'profile_interval' => $PopulateData['interval'],
			)
		);

		$this->formModel->setPopulateData($Data);
		$form = $this->formModel->initForm();

		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$Data = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);

			$this->model->edit($Data);
			App::redirect(__ADMINPANE__ . '/exchangexml/index');
		}
		else{

			$this->renderLayout(array(
				'form' => $form->Render())
			);
		}
	}

	public function runoperation ()
	{
		if ( $this->model->queueOperation($this->registry->core->getParam())) {
			App::getContainer()->get('session')->setVolatileMessage('Operacja została dodana do kolejki');
		}
		else {
			$msg = App::getContainer()->get('session')->getVolatileMessage();
			if ( !$msg) {
				App::getContainer()->get('session')->setVolatileMessage('Wystąpił problem podczas dodawania operacji do kolejki');
			}
		}

		App::redirect(__ADMINPANE__ . '/exchangexml/index');
	}

	public function download ()
	{
		$this->model->downloadFile($this->registry->core->getParam());
	}

	public function info ()
	{
		if (($info = $this->model->getOperationById((int) $this->id)) === FALSE ) {
			App::getContainer()->get('session')->setVolatileMessage('Podana operacja nie istnieje');
			App::redirect(__ADMINPANE__ . '/exchangexml/index');
		}

		if (empty($info['log'])) {
			$info['log'] = 'Dziennik zdarzeń jest pusty';
		}

        	$form = new FormEngine\Elements\Form(array(
			'name' => 'exchange',
			'action' => '',
			'method' => 'post'
		));

		$field = $form->AddChild(new FormEngine\Elements\Fieldset(array(
			'name' => 'exchange_data',
			'label' => $info['name']
		)));

		$field->AddChild(new FormEngine\Elements\TextField(array(
			'name' => 'url',
			'label' => $this->trans('TXT_EXCHANGE_REMOTE_FILE')
		)));

		$field->addChild(new FormEngine\Elements\Tip(array(
			'tip' => '<p>Podany adres należy uruchomić manualnie. Aby przetworzyć wybraną ilość rekordów ustaw:
				<ul>
					<li><b>limit</b> - ilość rekordów do przetworzenia</li>
					<li><b>offset</b> - ilość rekordów do pominięcia</li>
				</ul>
			</p>',
			'direction' => FormEngine\Elements\Tip::DOWN

		)));

		$field->AddChild(new FormEngine\Elements\TextField(array(
			'name' => 'runoperation',
			'label' => $this->trans('TXT_EXCHANGE_MANUAL_CALL'),
		)));

		$field->AddChild(new FormEngine\Elements\Textarea(array(
			'name' => 'info',
			'label' => $this->trans('TXT_LOGS'),
			'rows' => 15,
		)));

		$form->populate(array(
			'exchange_data' => array(
				'runoperation' => $this->registry->router->generate('admin', true, Array(
					'controller' => 'exchangexml',
					'action' => 'runoperation',
					'param' => $info['idexchange']
				)) . '?limit=' . $info['limit'] . '&offset=' . $info['offset'],
				'url' => $info['url'],
				'info' => $info['log'],
			)
		));

		$this->renderLayout(array(
			'form' => $form->Render())
		);
	}
}