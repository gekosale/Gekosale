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

namespace Gekosale\Component\Technicaldata\Controller\Admin;
use Gekosale\Core\Component\Controller\Admin;
use FormEngine;

class TechnicalData extends Admin
{

	public function index ()
	{
		
		$this->registry->xajaxInterface->registerFunction(Array(
			'AddGroup',
			$this->model,
			'addEmptyGroup'
		));
		
		$groups = $this->model->getAllTechnicalDataName();
		
		if ((int) $this->registry->core->getParam() == 0 && ! empty($groups) && isset($groups[0]['id'])){
			App::redirect(__ADMINPANE__ . '/technicaldata/edit/' . $groups[0]['id']);
		}
		
		$this->renderLayout(Array(
			'existingGroups' => $this->model->getAllTechnicalDataName()
		));
	
	}

	public function edit ()
	{
		$rawTechnicalDataData = $this->model->getGroup($this->id);
		if (empty($rawTechnicalDataData)){
			App::redirect(__ADMINPANE__ . '/technicaldata/');
		}
		
		$this->registry->xajaxInterface->registerFunction(Array(
			'AddGroup',
			$this->model,
			'addEmptyGroup'
		));
		
		$this->registry->xajaxInterface->registerFunction(Array(
			'DeleteSet',
			$this->model,
			'DeleteSet'
		));
		
		$populateData = Array(
			'group_data' => Array(
				'attributegroupname' => $rawTechnicalDataData['name'],
			),
			'attribute_data' => Array(
				'attributes' => $rawTechnicalDataData['attributes']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editTechnicalData($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/technicaldata/edit/' . $this->id);
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render(),
			'existingGroups' => $this->model->getAllTechnicalDataName(),
			'currentGroup' => $rawTechnicalDataData
		));
	
	}
}