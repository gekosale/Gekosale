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

namespace Gekosale;
use FormEngine;

class GroupsForm extends Component\Form
{
	
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$controllers = Array();
		$controllersRaw = App::getModel('groups')->getFullPermission();
		
		foreach ($controllersRaw as $controller){
			$controllers[] = Array(
				'name' => $controller['name'],
				'id' => $controller['id']
			);
		}
		
		$actions = Array();
		$actionsRaw = App::getContainer()->get('right')->getRightsToSmarty();
		foreach ($actionsRaw as $right){
			$actions[] = Array(
				'name' => $right['name'],
				'id' => $right['value']
			);
		}
		
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'group',
			'action' => '',
			'method' => 'post'
		));
		
		$basicData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'basic_data',
			'label' => $this->trans('TXT_BASIC_GROUP_DATA')
		)));
		
		$basicData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'name',
			'label' => $this->trans('TXT_GROUP_NAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_GROUP_NAME')),
				new FormEngine\Rules\Unique($this->trans('ERR_DUPLICATE_GROUP_NAME'), '`group`', 'name', null, Array(
					'column' => 'idgroup',
					'values' => (int) $this->registry->core->getParam()
				))
			)
		)));
		
		$rightsData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'rights_data',
			'label' => $this->trans('TXT_RIGHTS')
		)));
		
		$rightsData->AddChild(new FormEngine\Elements\RightsTable(Array(
			'name' => 'rights',
			'label' => $this->trans('TXT_GROUP_RIGHTS'),
			'controllers' => $controllers,
			'actions' => $actions
		)));
		
		$Data = Event::dispatch($this, 'admin.groups.initForm', Array(
			'form' => $form,
			'id' => (int) $this->registry->core->getParam(),
			'data' => $this->populateData
		));
		
		if (! empty($Data)){
			$form->Populate($Data);
		}
		
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		return $form;
	}

}