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
 * $Id: poll.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;
use FormEngine;
use sfEvent;

class pollController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'LoadAllPoll',
			$this->model,
			'getPollForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetQuestionsSuggestions',
			$this->model,
			'getQuestionsForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'doDeletePoll',
			$this->model,
			'doAJAXDeletePoll'
		));
		
		$this->registry->xajax->registerFunction(array(
			'disablePoll',
			$this->model,
			'doAJAXDisablePoll'
		));
		
		$this->registry->xajax->registerFunction(array(
			'enablePoll',
			$this->model,
			'doAJAXEnablePoll'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->addNewPoll($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/poll/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/poll');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		$rawPollData = $this->model->getPollView($this->id);
		
		$populateData = Array(
			'required_data' => Array(
				'publish' => $rawPollData['publish'],
				'lang_data' => $rawPollData['language']
			),
			'answers_book' => Array(
				'answers_data' => $rawPollData['answers']
			),
			'view_data' => Array(
				'view' => $rawPollData['view']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editPoll($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/poll');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}