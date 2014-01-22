<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o
 * licencji i autorach.
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
 * $Id: category.php 612 2011-11-28 20:02:10Z gekosale $
 */
namespace Gekosale\Plugin;

use FormEngine;

class CategoryController extends Component\Controller\Admin
{

	public function __construct ($registry)
	{
		parent::__construct($registry);

		$this->registry->xajaxInterface->registerFunction(Array(
			'DeleteCategory',
			$this->model,
			'deleteCategory'
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

		$this->registry->xajax->registerFunction(Array(
			'DuplicateCategory',
			$this->model,
			'duplicateCategory'
		));

		$this->registry->xajaxInterface->registerFunction(array(
			'doAJAXCreateSeoCategory',
			App::getModel('seo'),
			'doAJAXCreateSeoCategory'
		));

		$this->registry->xajax->registerFunction(array(
			'doAJAXRefreshSeoCategory',
			App::getModel('seo'),
			'doAJAXRefreshSeoCategory'
		));
	}

	public function index ()
	{
		App::getModel('contextmenu')->add($this->trans('TXT_PRODUCT_DATA'), $this->registry->router->url('admin', 'product'));
		App::getModel('contextmenu')->add($this->trans('TXT_PRICE_COMPARISONS'), $this->registry->router->url('admin', 'integration'));
		App::getModel('contextmenu')->add($this->trans('TXT_SITEMAPS'), $this->registry->router->url('admin', 'sitemaps'));

		$tree = new FormEngine\Elements\Form(Array(
			'name' => 'category_tree',
			'class' => 'category-select',
			'action' => '',
			'method' => 'post'
		));

		$categories = $this->model->getChildCategories();

		if ($this->id == '' && ! empty($categories)){
			App::redirect(__ADMINPANE__ . '/category/edit/' . current(array_keys($categories)));
		}

		$tree->AddChild(new FormEngine\Elements\Tree(Array(
			'name' => 'categories',
			'label' => $this->trans('TXT_CATEGORIES'),
			'add_item_prompt' => $this->trans('TXT_ENTER_NEW_CATEGORY_NAME'),
			'sortable' => true,
			'selectable' => false,
			'clickable' => true,
			'deletable' => true,
			'addable' => true,
			'prevent_duplicates' => true,
			'items' => $categories,
			'load_children' => Array(
				$this->model,
				'getChildCategories'
			),
			'onClick' => 'openCategoryEditor',
			'onAdd' => 'xajax_AddCategory',
			'addLabel' => $this->trans('TXT_ADD_CATEGORY'),
			'onAfterAdd' => 'openCategoryEditor',
			'onDelete' => 'xajax_DeleteCategory',
			'onAfterDelete' => 'openCategoryEditor',
			'onSaveOrder' => 'xajax_ChangeCategoryOrder'
		)));

		$this->registry->template->assign('tree', $tree->Render());

		$this->renderLayout(array());
	}

	public function edit ()
	{
		$rawCategoryData = $this->model->getCategoryView($this->id);
		if (empty($rawCategoryData)){
			App::redirect(__ADMINPANE__ . '/category');
		}

		$tree = new FormEngine\Elements\Form(Array(
			'name' => 'category_tree',
			'class' => 'category-select',
			'action' => '',
			'method' => 'post'
		));

		$categories = $this->model->getChildCategories(0, Array(
			$this->id
		));

		$tree->AddChild(new FormEngine\Elements\Tree(Array(
			'name' => 'categories',
			'label' => $this->trans('TXT_CATEGORIES'),
			'add_item_prompt' => $this->trans('TXT_ENTER_NEW_CATEGORY_NAME'),
			'sortable' => true,
			'selectable' => false,
			'clickable' => true,
			'deletable' => true,
			'addable' => true,
			'prevent_duplicates' => true,
			'items' => $categories,
			'load_children' => Array(
				$this->model,
				'getChildCategories'
			),
			'onClick' => 'openCategoryEditor',
			'onDuplicate' => 'xajax_DuplicateCategory',
			'onSaveOrder' => 'xajax_ChangeCategoryOrder',
			'onAdd' => 'xajax_AddCategory',
			'onAfterAdd' => 'openCategoryEditor',
			'onDelete' => 'xajax_DeleteCategory',
			'onAfterDelete' => 'openCategoryEditor',
			'onAfterDeleteId' => $rawCategoryData['next'],
			'active' => $this->id
		)));

		$populateData = Array(
			'required_data' => Array(
				'language_data' => $rawCategoryData['language'],
				'categoryid' => $rawCategoryData['catid'],
				'distinction' => $rawCategoryData['distinction'],
				'enable' => $rawCategoryData['enable']
			),
			'meta_data' => Array(
				'language_data' => $rawCategoryData['language']
			),
			'description_pane' => Array(
				'language_data' => $rawCategoryData['language']
			),
			'photos_pane' => Array(
				'photo' => $rawCategoryData['photoid']
			),
			'category_products' => Array(
				'products' => App::getModel('category')->getProductsDataGrid((int) $this->id)
			),
			'view_data' => Array(
				'view' => $rawCategoryData['view']
			)
		);

		$this->formModel->setPopulateData($populateData);

		$form = $this->formModel->initForm();

		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$formData = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
				$this->model->editCategory($formData, $this->id);
				App::getContainer()->get('session')->setVolatileMessage("Zapisano zmiany w kategorii.");
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/category/edit/' . $this->id);
		}

		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('id', $this->id);
		$this->registry->template->assign('tree', $tree->Render());
		$this->registry->template->assign('form', $form->Render());
		$this->registry->template->assign('total', count($categories));
		$this->registry->template->assign('categoryLink', App::getURLAdress() . Seo::getSeo('categorylist') . '/' . (isset($rawCategoryData['language'][Helper::getLanguageId()]['seo']) ? $rawCategoryData['language'][Helper::getLanguageId()]['seo'] : $rawCategoryData['language'][1]['seo']));
		$this->registry->template->assign('categoryName', $rawCategoryData['language'][Helper::getLanguageId()]['name']);
		$this->registry->template->display($this->loadTemplate('edit.tpl'));
	}

	public function duplicate ()
	{
		$tree = new FormEngine\Elements\Form(Array(
			'name' => 'category_tree',
			'class' => 'category-select',
			'action' => '',
			'method' => 'post'
		));

		$categories = $this->model->getChildCategories(0, Array(
			$this->id
		));

		$tree->AddChild(new FormEngine\Elements\Tree(Array(
			'name' => 'categories',
			'label' => $this->trans('TXT_CATEGORIES'),
			'add_item_prompt' => $this->trans('TXT_ENTER_NEW_CATEGORY_NAME'),
			'sortable' => true,
			'selectable' => false,
			'clickable' => true,
			'deletable' => true,
			'addable' => true,
			'prevent_duplicates' => true,
			'items' => $categories,
			'load_children' => Array(
				$this->model,
				'getChildCategories'
			),
			'onClick' => 'openCategoryEditor',
			'onDuplicate' => 'openCategoryEditorDuplicate',
			'onSaveOrder' => 'xajax_ChangeCategoryOrder',
			'onAdd' => 'xajax_AddCategory',
			'onAfterAdd' => 'openCategoryEditor',
			'onDelete' => 'xajax_DeleteCategory',
			'onAfterDelete' => 'openCategoryEditor',
			'active' => $this->id
		)));

		$rawCategoryData = $this->model->getCategoryView($this->id);

		$populateData = Array(
			'required_data' => Array(
				'language_data' => $rawCategoryData['language'],
				'categoryid' => $rawCategoryData['catid'],
				'distinction' => $rawCategoryData['distinction'],
				'enable' => $rawCategoryData['enable']
			),
			'meta_data' => Array(
				'language_data' => $rawCategoryData['language']
			),
			'description_pane' => Array(
				'language_data' => $rawCategoryData['language']
			),
			'photos_pane' => Array(
				'photo' => $rawCategoryData['photoid']
			),
			'category_products' => Array(
				'products' => App::getModel('category')->getProductsDataGrid((int) $this->id)
			),
			'view_data' => Array(
				'view' => $rawCategoryData['view']
			)
		);

		$this->formModel->setPopulateData($populateData);

		$form = $this->formModel->initForm();

		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{

				$formData = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
				$this->model->duplicateCategory($formData);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/category');
		}

		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('id', $this->id);
		$this->registry->template->assign('tree', $tree->Render());
		$this->registry->template->assign('form', $form->Render());
		$this->registry->template->assign('total', count($categories));
		$this->registry->template->assign('categoryName', $rawCategoryData['language'][Helper::getLanguageId()]['name']);
		$this->registry->template->display($this->loadTemplate('duplicate.tpl'));
	}
}