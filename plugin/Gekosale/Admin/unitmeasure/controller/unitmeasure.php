<?php
/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms 
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the 
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 * 
 * $Revision: 109 $
 * $Author: gekosale $
 * $Date: 2011-05-06 21:41:22 +0200 (Pt, 06 maj 2011) $
 * $Id: unitmeasure.php 109 2011-05-06 19:41:22Z gekosale $ 
 */

namespace Gekosale;

class UnitMeasureController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'LoadAllUnitMeasure',
			$this->model,
			'getUnitMeasureForAjax'
		));
		$this->registry->xajax->registerFunction(array(
			'doDeleteUnitMeasure',
			$this->model,
			'doAJAXDeleteUnitMeasure'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		$form = $this->formModel->initForm();
		
		if ($form->Validate(\FormEngine\FE::SubmittedData())){
			$this->model->addUnitMeasure($form->getSubmitValues(\FormEngine\Elements\Form::FORMAT_FLAT));
			if (\FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/unitmeasure/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/unitmeasure');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		
		$rawUnitmeasureData = $this->model->getUnitMeasureView($this->id);
		
		$populateData = Array(
			'required_data' => Array(
				'language_data' => $rawUnitmeasureData['language']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(\FormEngine\FE::SubmittedData())){
			try{
				$this->model->editUnitMeasure($form->getSubmitValues(\FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/unitmeasure');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}