<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace Gekosale;

use FormEngine;

class AllegroOptionsTemplateController extends Component\Controller\Admin
{

	public function index ()
	{
		try {
			$this->registry->xajax->registerFunction(array(
				'doDeleteAllegrooptionstemplate',
				App::getModel('allegro/allegrooptionstemplate'),
				'doAJAXDeleteAllegrooptionstemplate'
			));

			$this->registry->xajax->registerFunction(array(
				'LoadAllAllegrooptionstemplate',
				App::getModel('allegro/allegrooptionstemplate'),
				'getAllegrooptionstemplateForAjax'
			));

			$this->registry->xajax->registerFunction(array(
				'GetAllegrooptionstemplateSuggestions',
				App::getModel('allegro/allegrooptionstemplate'),
				'getAllegrooptionstemplateForAjax'
			));

			$this->renderLayout(array(
				'datagrid_filter' => App::getModel('allegro/allegrooptionstemplate')->getDatagridFilterData()
			));
		}
		catch(\Exception $e) {
			$this->renderLayout(array(
				'errormsg' => $e->getMessage()
			));
		}
	}

	public function add ()
	{
		try {
			$form = $this->formModel->initForm();

			if ($form->Validate(FormEngine\FE::SubmittedData())){
				$Data = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
				App::getModel('allegro/allegrooptionstemplate')->addAllegroOptionsTemplate($Data['optionstemplate'], $Data);
				App::redirect(__ADMINPANE__ . '/allegrooptionstemplate');
			}

			$this->renderLayout(array(
				'form' => $form->Render()
			));
		}
		catch(\Exception $e) {
			$this->renderLayout(array(
				'errormsg' => $e->getMessage()
			));
		}
	}

	public function edit ()
	{
		try {
			$rawData = App::getModel('allegro/allegrooptionstemplate')->getAllegrooptionstemplateById((int) $this->registry->core->getParam());
			$populateData = Array(
				'optionstemplate_data' => Array(
					'optionstemplate' => $rawData['optionstemplate']
				),
				'main_data' => Array(
					'sell-form-id-1' => $rawData['sell-form-id-1'],
					'sell-form-id-3' => $rawData['sell-form-id-3'],
					'sell-form-id-4' => $rawData['sell-form-id-4'],
					'sell-form-id-29' => $rawData['sell-form-id-29']
				),
				'seller_data' => Array(
					'sell-form-id-9' => $rawData['sell-form-id-9'],
					'sell-form-id-10' => $rawData['sell-form-id-10'],
					'sell-form-id-32' => $rawData['sell-form-id-32'],
					'sell-form-id-11' => $rawData['sell-form-id-11']
				),
				'delivery_data' => Array(
					'sell-form-id-12' => $rawData['sell-form-id-12'],
					'sell-form-id-13' => $rawData['sell-form-id-13'],
					'sell-form-id-35' => $rawData['sell-form-id-35'],
					'sell-form-id-36' => $rawData['sell-form-id-36'],
					'sell-form-id-36-cost' => $rawData['sell-form-id-36-cost'],
					'sell-form-id-37' => $rawData['sell-form-id-37'],
					'sell-form-id-37-cost' => $rawData['sell-form-id-37-cost'],
					'sell-form-id-38' => $rawData['sell-form-id-38'],
					'sell-form-id-38-cost' => $rawData['sell-form-id-38-cost'],
					'sell-form-id-39' => $rawData['sell-form-id-39'],
					'sell-form-id-39-cost' => $rawData['sell-form-id-39-cost'],
					'sell-form-id-40' => $rawData['sell-form-id-40'],
					'sell-form-id-40-cost' => $rawData['sell-form-id-40-cost'],
					'sell-form-id-41' => $rawData['sell-form-id-41'],
					'sell-form-id-41-cost' => $rawData['sell-form-id-41-cost'],
					'sell-form-id-42' => $rawData['sell-form-id-42'],
					'sell-form-id-42-cost' => $rawData['sell-form-id-42-cost'],
					'sell-form-id-43' => $rawData['sell-form-id-43'],
					'sell-form-id-43-cost' => $rawData['sell-form-id-43-cost'],
					'sell-form-id-44' => $rawData['sell-form-id-44'],
					'sell-form-id-44-cost' => $rawData['sell-form-id-44-cost'],
					'sell-form-id-45' => $rawData['sell-form-id-45'],
					'sell-form-id-45-cost' => $rawData['sell-form-id-45-cost'],
					'sell-form-id-46' => $rawData['sell-form-id-46'],
					'sell-form-id-46-cost' => $rawData['sell-form-id-46-cost'],
					'sell-form-id-47' => $rawData['sell-form-id-47'],
					'sell-form-id-47-cost' => $rawData['sell-form-id-47-cost'],
					'sell-form-id-48' => $rawData['sell-form-id-48'],
					'sell-form-id-48-cost' => $rawData['sell-form-id-48-cost'],
					'sell-form-id-49' => $rawData['sell-form-id-49'],
					'sell-form-id-49-cost' => $rawData['sell-form-id-49-cost'],
					'sell-form-id-50' => $rawData['sell-form-id-50'],
					'sell-form-id-50-cost' => $rawData['sell-form-id-50-cost'],
					'sell-form-id-51' => $rawData['sell-form-id-51'],
					'sell-form-id-51-cost' => $rawData['sell-form-id-51-cost'],
					'sell-form-id-52' => $rawData['sell-form-id-52'],
					'sell-form-id-52-cost' => $rawData['sell-form-id-52-cost']
				),
				'payment_data' => Array(
					'sell-form-id-14' => $rawData['sell-form-id-14'],
					'sell-form-id-27' => $rawData['sell-form-id-27']
				),
				'additional_data' => Array(
					'sell-form-id-15' => $rawData['sell-form-id-15']
				),
				'price_data' => Array(
					'sell-form-id-6' => $rawData['sell-form-id-6'],
					'sell-form-id-7' => $rawData['sell-form-id-7'],
					'sell-form-id-8' => $rawData['sell-form-id-8']
				),
				'template_data' => Array(
					'content' => $rawData['content']
				)
			);

			$this->formModel->setPopulateData($populateData);

			$form = $this->formModel->initForm();

			if ($form->Validate(FormEngine\FE::SubmittedData())){
				$Data = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
				App::getModel('allegro/allegrooptionstemplate')->editAllegroOptionsTemplate($Data['optionstemplate'], $Data, (int) $this->registry->core->getParam());
				App::redirect(__ADMINPANE__ . '/allegrooptionstemplate');
			}

			$this->renderLayout(array(
				'form' => $form->Render()
			));
		}
		catch(\Exception $e) {
			$this->renderLayout(array(
				'errormsg' => $e->getMessage()
			));
		}
	}
}