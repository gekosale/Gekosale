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
 * $Id: language.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;
use FormEngine;

class LanguageController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doDeleteLanguage',
			$this->model,
			'doAJAXDeleteLanguage'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllLanguage',
			$this->model,
			'getLanguageForAjax'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->addNewLanguage($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/language/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/language');
			}
		
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		
		$rawLanguageData = $this->model->getLanguageView($this->id);
		
		if(empty($rawLanguageData)){
			App::redirect(__ADMINPANE__ . '/language');
		}
		
		$populateData = Array(
			'required_data' => Array(
				'name' => $rawLanguageData['name'],
				'translation' => $rawLanguageData['translation']
			),
			'currency_data' => Array(
				'currencyid' => $rawLanguageData['currencyid']
			),
			'flag_pane' => Array(
				'flag' => $rawLanguageData['flag']
			),
			'view_data' => Array(
				'view' => $rawLanguageData['view']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editLanguage($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/language');
		
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}