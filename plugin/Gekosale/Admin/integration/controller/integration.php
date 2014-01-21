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
 * $Id: integration.php 552 2011-10-08 15:56:59Z gekosale $ 
 */
namespace Gekosale;

use FormEngine;
use sfEvent;

class IntegrationController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'LoadAllIntegration',
			$this->model,
			'getIntegrationForAjax'
		));
		$this->registry->xajax->registerFunction(array(
			'disableIntegration',
			$this->model,
			'doAJAXDisableIntegration'
		));
		$this->registry->xajax->registerFunction(array(
			'enableIntegration',
			$this->model,
			'doAJAXEnableIntegration'
		));
		$this->renderLayout(array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function edit ()
	{
		$integrationModel = $this->model->getIntegrationModelById($this->id);
		
		if (empty($integrationModel)){
			App::redirect(__ADMINPANE__ . '/integration');
		}
		
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'edit_integration',
			'action' => '',
			'method' => 'post'
		));
		
		if (method_exists(App::getModel('integration/' . $integrationModel), 'updateCategories')){
			App::getModel('integration/' . $integrationModel)->updateCategories();
		}
		
		$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'required_data',
			'label' => $this->trans('TXT_MAIN_INFORMATION')
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\StaticText(Array(
			'text' => '<p>' . App::getModel('integration/' . $integrationModel)->getDescription() . '</p>'
		)));
		
		$url = $this->registry->router->generate('frontend.integration', true, Array(
			'param' => $integrationModel
		));
		
		$requiredData->AddChild(new FormEngine\Elements\StaticText(Array(
			'text' => '<p><a href="' . $url . '" target="_blank"><b>Link do pliku integracyjnego</b></a></p>'
		)));
		
		$configurationFields = App::getModel('integration/' . $integrationModel)->getConfigurationFields();
		
		if (is_array($configurationFields) && ! empty($configurationFields)){
			$configurationData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
				'name' => 'configuration_data',
				'label' => $this->trans('TXT_CONFIGURATION_DATA')
			)));
		}
		
		$whitelist = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'whitelist_data',
			'label' => $this->trans('TXT_INTEGRATION_WHITELIST')
		)));
		
		$whitelist->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>' . $this->trans('TXT_INTEGRATION_WHITELIST_HELP') . '</p>'
		)));
		
		$whitelist->AddChild(new FormEngine\Elements\StaticText(Array(
			'text' => '<strong>Twój adres IP: ' . Core::getRealIpAddress() . '</strong>'
		)));
		
		$fieldset = $whitelist->AddChild(new FormEngine\Elements\FieldsetRepeatable(Array(
			'name' => 'whitelist',
			'label' => $this->trans('TXT_INTEGRATION_WHITELIST'),
			'repeat_min' => 1,
			'repeat_max' => FormEngine\FE::INFINITE
		)));
		
		$fieldset->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'ip',
			'label' => 'IP'
		)));
		
		$rawData = $this->model->getIntegrationView($this->id);
		
		$pollData = Array(
			'whitelist_data' => Array(
				'whitelist' => $rawData['whitelist']
			)
		);
		
		$form->Populate($pollData);
		
		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				
				$this->model->editIntegration($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/integration');
		}
		
		$this->registry->template->assign('form', $form->Render());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->display($this->loadTemplate('edit.tpl'));
	}
}