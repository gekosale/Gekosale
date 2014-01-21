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
 * $Revision: 464 $
 * $Author: gekosale $
 * $Date: 2011-08-31 08:19:48 +0200 (Śr, 31 sie 2011) $
 * $Id: attributegroup.php 464 2011-08-31 06:19:48Z gekosale $
 */

namespace Gekosale;
use FormEngine;

class AttributeGroupController extends Component\Controller\Admin
{

	public function index ()
	{
		
		$this->registry->xajaxInterface->registerFunction(Array(
			'AddGroup',
			$this->model,
			'addEmptyGroup'
		));
		
		$groups = $this->model->getAllAttributeGroupName();
		
		if ((int) $this->registry->core->getParam() == 0 && ! empty($groups) && isset($groups[0]['id'])){
			App::redirect(__ADMINPANE__ . '/attributegroup/edit/' . $groups[0]['id']);
		}
		
		$this->renderLayout(Array(
			'existingGroups' => $this->model->getAllAttributeGroupName()
		));
	
	}

	public function edit ()
	{
		$rawAttributeGroupData = $this->model->getGroup($this->id);
		if (empty($rawAttributeGroupData)){
			App::redirect(__ADMINPANE__ . '/attributegroup/');
		}
		
		$this->registry->xajaxInterface->registerFunction(Array(
			'AddGroup',
			$this->model,
			'addEmptyGroup'
		));
		
		$this->registry->xajaxInterface->registerFunction(Array(
			'DeleteGroup',
			$this->model,
			'deleteGroup'
		));
		
		$populateData = Array(
			'group_data' => Array(
				'attributegroupname' => $rawAttributeGroupData['name'],
				'category' => $rawAttributeGroupData['category']
			),
			'attribute_data' => Array(
				'attributes' => $rawAttributeGroupData['attributes']
			)
		);

		$error = App::getContainer()->get('session')->getVolatileErrorMessage();
		if ($error){
			$this->registry->template->assign('errormessage', print_r($error, 1));
		}

		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editAttributeGroup($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/attributegroup/edit/' . $this->id);
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render(),
			'existingGroups' => $this->model->getAllAttributeGroupName(),
			'currentGroup' => $rawAttributeGroupData
		));
	
	}
}