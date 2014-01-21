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
 * $Id: groups.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;
use FormEngine;

class GroupsController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doDeleteGroup',
			$this->model,
			'doAJAXDeleteGroups'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllGroups',
			$this->model,
			'getGroupsForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetNameSuggestions',
			$this->model,
			'getNameForAjax'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->add($form->getSubmitValues());
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/groups/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/groups');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		$rawGroupData = $this->model->getGroupsView((int) $this->id);
		if(empty($rawGroupData)){
			App::redirect(__ADMINPANE__ . '/groups');
		}
		
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
		
		$rightsData = Array();
		foreach ($controllersRaw as $controller){
			$mask = 1;
			$rights = Array();
			for ($i = 0; $i < count($actions); $i ++){
				$rights[$actions[$i]['id']] = ($controller['permission'] & $mask) ? 1 : 0;
				$mask = $mask << 1;
			}
			$rightsData[$controller['id']] = $rights;
		}
		
		$populateData = Array(
			'basic_data' => Array(
				'name' => $rawGroupData['name']
			),
			'rights_data' => Array(
				'rights' => $rightsData
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->editPermission($form->getSubmitValues(), $this->id);
			App::redirect(__ADMINPANE__ . '/groups');
		
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	
	}
}