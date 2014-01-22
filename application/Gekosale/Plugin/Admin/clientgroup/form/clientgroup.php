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

class ClientGroupForm extends Component\Form
{
	
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'clientgroup',
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
			'label' => $this->trans('TXT_GROUP_NAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_GROUP_NAME')),
				new FormEngine\Rules\LanguageUnique($this->trans('ERR_GROUP_NAME_ALREADY_EXISTS'), 'clientgrouptranslation', 'name', null, Array(
					'column' => 'clientgroupid',
					'values' => (int) $this->registry->core->getParam()
				))
			)
		)));
		
		$clientsData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'clients_data',
			'label' => $this->trans('TXT_CLIENTS_SELECTION')
		)));
		
		$clientsData->AddChild(new FormEngine\Elements\DatagridSelect(Array(
			'name' => 'clients',
			'label' => $this->trans('TXT_SELECT_CLIENTS'),
			'key' => 'idclient',
			'datagrid_init_function' => Array(
				App::getModel('client/client'),
				'initDatagrid'
			),
			'repeat_max' => FormEngine\FE::INFINITE,
			'columns' => $this->getClientDatagridColumns()
		)));
		
		$Data = Event::dispatch($this, 'admin.clientgroup.initForm', Array(
			'form' => $form,
			'id' => (int) $this->registry->core->getParam(),
			'data' => $this->populateData
		));
		
		if (! empty($Data)){
			$form->Populate($Data);
		}
		
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		return $form;
	}

	protected function getClientDatagridColumns ()
	{
		return Array(
			Array(
				'id' => 'idclient',
				'caption' => $this->trans('TXT_ID'),
				'appearance' => Array(
					'width' => 90
				),
				'filter' => Array(
					'type' => FormEngine\Elements\DatagridSelect::FILTER_BETWEEN
				)
			),
			Array(
				'id' => 'clientorder',
				'caption' => $this->trans('TXT_CLIENTORDER_VALUE'),
				'appearance' => Array(
					'width' => 60,
					'visible' => false,
					'align' => FormEngine\Elements\DatagridSelect::ALIGN_RIGHT
				),
				'filter' => Array(
					'type' => FormEngine\Elements\DatagridSelect::FILTER_BETWEEN
				)
			),
			Array(
				'id' => 'firstname',
				'caption' => $this->trans('TXT_FIRSTNAME'),
				'appearance' => Array(
					'width' => 160,
					'align' => FormEngine\Elements\DatagridSelect::ALIGN_LEFT
				),
				'filter' => Array(
					'type' => FormEngine\Elements\DatagridSelect::FILTER_INPUT
				)
			),
			Array(
				'id' => 'surname',
				'caption' => $this->trans('TXT_SURNAME'),
				'appearance' => Array(
					'width' => FormEngine\Elements\DatagridSelect::WIDTH_AUTO,
					'align' => FormEngine\Elements\DatagridSelect::ALIGN_LEFT
				),
				'filter' => Array(
					'type' => FormEngine\Elements\DatagridSelect::FILTER_INPUT
				)
			),
			Array(
				'id' => 'email',
				'caption' => $this->trans('TXT_EMAIL'),
				'appearance' => Array(
					'width' => 140,
					'align' => FormEngine\Elements\DatagridSelect::ALIGN_LEFT,
					'visible' => false
				),
				'filter' => Array(
					'type' => FormEngine\Elements\DatagridSelect::FILTER_INPUT
				)
			),
			Array(
				'id' => 'phone',
				'caption' => $this->trans('TXT_PHONE'),
				'appearance' => Array(
					'width' => 110
				),
				'filter' => Array(
					'type' => FormEngine\Elements\DatagridSelect::FILTER_INPUT
				)
			)
		);
	}

}