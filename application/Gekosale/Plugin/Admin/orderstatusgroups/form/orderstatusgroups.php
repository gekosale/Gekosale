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
 * $Id: news.php 619 2011-12-19 21:09:00Z gekosale $ 
 */
namespace Gekosale\Plugin;

use FormEngine;

class OrderStatusGroupsForm extends Component\Form
{
	protected $registry;
	protected $populateData;

	public function __construct ($registry)
	{
		parent::__construct($registry);
		$this->registry = $registry;
	}

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'orderstatusgroups',
			'action' => '',
			'method' => 'post'
		));
		
		$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'required_data',
			'label' => $this->trans('TXT_MAIN_DATA')
		)));
		
		$languageData = $requiredData->AddChild(new FormEngine\Elements\FieldsetLanguage(Array(
			'name' => 'language_data',
			'label' => $this->trans('TXT_LANGUAGE_DATA')
		)));
		
		$languageData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'name',
			'label' => $this->trans('TXT_NAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME')),
				new FormEngine\Rules\LanguageUnique($this->trans('ERR_TOPIC_ALREADY_EXISTS'), 'orderstatusgroupstranslation', 'name', null, Array(
					'column' => 'orderstatusgroupsid',
					'values' => (int) $this->registry->core->getParam()
				))
			)
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\MultiSelect(Array(
			'name' => 'orderstatus',
			'label' => $this->trans('TXT_ORDERSTATUS'),
			'options' => FormEngine\Option::Make(App::getModel('orderstatus')->getOrderStatusToSelect())
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\ColourSchemePicker(Array(
			'name' => 'colour',
			'label' => 'Kolor statusów zamówień',
			'type_colour' => true,
			'type_gradient' => false,
			'type_image' => false
		)));
		
		$Data = Event::dispatch($this, 'admin.orderstatusgroups.initForm', Array(
			'form' => $form,
			'id' => (int) $this->registry->core->getParam(),
			'data' => $this->populateData
		));
		
		if (! empty($Data)){
			$form->Populate($Data);
		}
		
		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		return $form;
	}
}