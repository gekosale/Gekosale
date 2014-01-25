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
 * $Id: clientgroup.php 464 2011-08-31 06:19:48Z gekosale $ 
 */

namespace Gekosale\Component\Clientgroup\Controller\Admin;
use Gekosale\Core\Component\Controller\Admin;
use FormEngine;

class Group extends Admin
{

	public function index ()
	{
        App::getModel('contextmenu')->add($this->trans('TXT_ORDERS'), $this->getRouter()->url('admin', 'order'));
        App::getModel('contextmenu')->add($this->trans('TXT_RULESCART'), $this->getRouter()->url('admin', 'rulescart'));
        
		$this->registry->xajax->registerFunction(array(
			'doDeleteClientGroup',
			$this->model,
			'doAJAXDeleteClientGroup'
		));
		$this->registry->xajax->registerFunction(array(
			'LoadAllClientGroup',
			$this->model,
			'getClientGroupForAjax'
		));
		$this->registry->xajax->registerFunction(array(
			'GetNameSuggestions',
			$this->model,
			'getNameForAjax'
		));
		
		$this->renderLayout();
	}

	public function add ()
	{
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->addClientGroup($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/clientgroup/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/clientgroup');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		
		$rawClientgroupData = $this->model->getClientGroupById($this->registry->core->getParam());
		
		$populateData = Array(
			'required_data' => Array(
				'language_data' => $rawClientgroupData['language']
			),
			'clients_data' => Array(
				'clients' => $rawClientgroupData['clients']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editClientGroup($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->registry->core->getParam());
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/clientgroup');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

}