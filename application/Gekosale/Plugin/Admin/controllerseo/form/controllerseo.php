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

class ControllerSeoForm extends Component\Form
{

	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'controllerseo',
			'action' => '',
			'method' => 'post'
		));

		$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'required_data',
			'label' => $this->trans('TXT_MAIN_DATA')
		)));

		$table = 'controllerseo CS INNER JOIN controller C ON C.idcontroller = CS.controllerid';
		$field = 'C.name';

		$requiredData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'controller',
			'label' => $this->trans('TXT_CONTROLLER'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_CONTROLLER')),
				new FormEngine\Rules\Format($this->trans('ERR_WRONG_FORMAT'), '/^[a-zA-Z]+$/'),
				new FormEngine\Rules\Unique($this->trans('ERR_NAME_ALREADY_EXISTS'), $table, $field, null, Array(
					'column' => 'CS.controllerid',
					'values' => (int) $this->registry->core->getParam()
				))
			)
		)));

		$languageData = $requiredData->AddChild(new FormEngine\Elements\FieldsetLanguage(Array(
			'name' => 'language_data',
			'label' => $this->trans('TXT_LANGUAGE_DATA')
		)));

		$languageData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'translation',
			'label' => $this->trans('TXT_TRANSLATION'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_TRANSLATION'))
			)
		)));

		$Data = Event::dispatch($this, 'admin.controllerseo.initForm', Array(
			'form' => $form,
			'id' => (int) $this->registry->core->getParam(),
			'data' => $this->populateData
		));

		if (! empty($Data)){
			$form->Populate($Data);
		}

		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());
		$form->AddFilter(new FormEngine\Filters\NoCode());

		return $form;
	}

}