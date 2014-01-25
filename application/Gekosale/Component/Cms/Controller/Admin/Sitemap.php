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
 * $Id: sitemaps.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Component\Sitemaps\Controller\Admin;
use Gekosale\Core\Component\Controller\Admin;
use FormEngine;

class Sitemap extends Admin
{

	public function index ()
	{
        App::getModel('contextmenu')->add($this->trans('TXT_CATEGORIES'), $this->getRouter()->url('admin', 'category'));
        App::getModel('contextmenu')->add($this->trans('TXT_PRODUCTS'), $this->getRouter()->url('admin', 'product'));
        
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllSitemaps',
			$this->model,
			'getSitemapsForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'doDeleteSitemaps',
			$this->model,
			'doAJAXDeleteSitemaps'
		));
		
		$this->registry->xajax->registerFunction(array(
			'refreshSitemaps',
			$this->model,
			'doAJAXRefreshSitemaps'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	
	}

	public function add ()
	{
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			
			$this->model->addSitemaps($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/sitemaps/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/sitemaps');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		
		$populateData = $this->model->getDataById($this->id);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			
			try{
				$this->model->editSitemaps($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/sitemaps');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}