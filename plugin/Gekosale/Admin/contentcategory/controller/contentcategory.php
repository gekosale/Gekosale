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
 * $Revision: 612 $
 * $Author: gekosale $
 * $Date: 2011-11-28 21:02:10 +0100 (Pn, 28 lis 2011) $
 * $Id: contentcategory.php 612 2011-11-28 20:02:10Z gekosale $ 
 */
namespace Gekosale;

use FormEngine;

class ContentCategoryController extends Component\Controller\Admin
{

	public function __construct ($registry)
	{
		parent::__construct($registry);
		
		App::getModel('contextmenu')->add($this->trans('TXT_PRODUCTS'), $this->getRouter()->url('admin', 'product'));
		App::getModel('contextmenu')->add($this->trans('TXT_CATEGORIES'), $this->getRouter()->url('admin', 'category'));
		App::getModel('contextmenu')->add($this->trans('TXT_SITEMAPS'), $this->registry->router->url('admin', 'sitemaps'));
		
		$this->registry->xajaxInterface->registerFunction(Array(
			'DeleteCategory',
			$this->model,
			'deleteContentCategory'
		));
		
		$this->registry->xajaxInterface->registerFunction(Array(
			'AddCategory',
			$this->model,
			'addEmptyCategory'
		));
		
		$this->registry->xajaxInterface->registerFunction(Array(
			'ChangeCategoryOrder',
			$this->model,
			'changeCategoryOrder'
		));
	}

	public function index ()
	{
		$tree = new FormEngine\Elements\Form(Array(
			'name' => 'category_tree',
			'class' => 'category-select',
			'action' => '',
			'method' => 'post'
		));
		
		$categories = $this->model->getContentCategoryALL();
		
		if (! strlen($this->id) && count($categories) > 0){
			App::redirect(__ADMINPANE__ . '/contentcategory/edit/' . current(array_keys($categories)));
		}
		$tree->AddChild(new FormEngine\Elements\Tree(Array(
			'name' => 'categories',
			'add_item_prompt' => $this->trans('TXT_NAME'),
			'sortable' => true,
			'selectable' => false,
			'clickable' => true,
			'deletable' => true,
			'addable' => true,
			'prevent_duplicates' => false,
			'items' => $categories,
			'onClick' => 'openCategoryEditor',
			'onAdd' => 'xajax_AddCategory',
			'addLabel' => $this->trans('TXT_ADD'),
			'onAfterAdd' => 'openCategoryEditor',
			'onDelete' => 'xajax_DeleteCategory',
			'onAfterDelete' => 'openCategoryEditor',
			'onSaveOrder' => 'xajax_ChangeCategoryOrder'
		)));
		
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('tree', $tree->Render());
		$this->registry->template->display($this->loadTemplate('index.tpl'));
	}

	public function edit ()
	{
		$tree = new FormEngine\Elements\Form(Array(
			'name' => 'category_tree',
			'class' => 'category-select',
			'action' => '',
			'method' => 'post'
		));
		
		$categories = $this->model->getContentCategoryALL();
		
		$rawContentcategoryData = $this->model->getContentCategoryView($this->id);
		
		$tree->AddChild(new FormEngine\Elements\Tree(Array(
			'name' => 'categories',
			'add_item_prompt' => $this->trans('TXT_NAME'),
			'sortable' => true,
			'selectable' => false,
			'clickable' => true,
			'deletable' => true,
			'addable' => true,
			'prevent_duplicates' => false,
			'items' => $categories,
			'onClick' => 'openCategoryEditor',
			'onAdd' => 'xajax_AddCategory',
			'addLabel' => $this->trans('TXT_ADD'),
			'onAfterAdd' => 'openCategoryEditor',
			'onDelete' => 'xajax_DeleteCategory',
			'onAfterDelete' => 'openCategoryEditor',
			'onSaveOrder' => 'xajax_ChangeCategoryOrder',
			'active' => $this->registry->core->getParam(),
			'onAfterDeleteId' => $rawContentcategoryData['next']
		)));
		
		$populateData = Array(
			'required_data' => Array(
				'footer' => $rawContentcategoryData['footer'],
				'header' => $rawContentcategoryData['header'],
				'contentcategoryid' => $rawContentcategoryData['contentcategory'],
				'language_data' => $rawContentcategoryData['language']
			),
			'meta_data' => Array(
				'language_data' => $rawContentcategoryData['language']
			),
			'redirect_data' => Array(
				'redirect' => $rawContentcategoryData['redirect'],
				'redirect_route' => $rawContentcategoryData['redirect_route'],
				'redirect_url' => $rawContentcategoryData['redirect_url']
			),
			'view_data' => Array(
				'view' => $rawContentcategoryData['view']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editContentCategory($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::getContainer()->get('session')->setVolatileMessage("Zapisano zmiany w stronie statycznej.");
			App::redirect(__ADMINPANE__ . '/contentcategory/edit/' . $this->id);
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render(),
			'tree' => $tree->Render(),
			'contentLink' => $this->registry->loader->getViewUrl() . Seo::getSeo('staticcontent') . '/' . $this->id . '/' . $rawContentcategoryData['seo']
		));
	}
}