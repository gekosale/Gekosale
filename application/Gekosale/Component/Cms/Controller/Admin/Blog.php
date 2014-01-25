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

namespace Gekosale\Component\News\Controller\Admin;
use Gekosale\Core\Component\Controller\Admin;

class Blog extends Admin
{

	public function index ()
	{
        
        App::getModel('contextmenu')->add($this->trans('TXT_NEWSLETTER'), $this->getRouter()->url('admin', 'newsletter'));
        App::getModel('contextmenu')->add($this->trans('TXT_PRODUCTS'), $this->getRouter()->url('admin', 'product'));
        App::getModel('contextmenu')->add($this->trans('TXT_CATEGORIES'), $this->getRouter()->url('admin', 'category'));
        App::getModel('contextmenu')->add($this->trans('TXT_SITEMAPS'), $this->registry->router->url('admin', 'sitemaps'));

        
        
		$this->registry->xajax->registerFunction(array(
			'LoadAllNews',
			$this->model,
			'getNewsForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetTopicSuggestions',
			$this->model,
			'getTopicForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'doDeleteNews',
			$this->model,
			'doAJAXDeleteNews'
		));
		
		$this->registry->xajax->registerFunction(array(
			'disableNews',
			$this->model,
			'doAJAXDisableNews'
		));
		
		$this->registry->xajax->registerFunction(array(
			'enableNews',
			$this->model,
			'doAJAXEnableNews'
		));
		
		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		$form = $this->formModel->initForm();
		
		if ($form->Validate(\FormEngine\FE::SubmittedData())){
			$this->model->addNewNews($form->getSubmitValues(\FormEngine\Elements\Form::FORMAT_FLAT));
			if (\FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/news/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/news');
			}
		}
		
		$this->registry->xajaxInterface->registerFunction(array(
			'doAJAXCreateSeo',
			App::getModel('seo'),
			'doAJAXCreateSeo'
		));
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		
		$rawNewsData = $this->model->getNewsView((int) $this->registry->core->getParam());
		
		$populateData = Array(
			'required_data' => Array(
				'publish' => $rawNewsData['publish'],
				'featured' => $rawNewsData['featured'],
				'language_data' => $rawNewsData['language']
			),
			'meta_data' => Array(
				'language_data' => $rawNewsData['language']
			),
			'additional_data' => array(
				'startdate' => $rawNewsData['startdate'],
				'enddate' => $rawNewsData['enddate'],
			),
			'photos_pane' => Array(
				'photo' => $rawNewsData['photo'],
				'mainphotoid' => $rawNewsData['mainphotoid'],
			),
			'view_data' => Array(
				'view' => $rawNewsData['view']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(\FormEngine\FE::SubmittedData())){
			try{
				$this->model->editNews($form->getSubmitValues(\FormEngine\Elements\Form::FORMAT_FLAT), $this->registry->core->getParam());
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/news');
		}
		
		$this->registry->xajaxInterface->registerFunction(array(
			'doAJAXCreateSeo',
			App::getModel('seo'),
			'doAJAXCreateSeo'
		));
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}