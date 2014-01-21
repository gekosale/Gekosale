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
 * $Id: rangetype.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;
use FormEngine;

class RangeTypeController extends Component\Controller\Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'LoadAllRangeType',
			App::getModel('rangetype'),
			'getRangeTypeForAjax'
		));
		$this->registry->xajax->registerFunction(array(
			'doDeleteRangeType',
			App::getModel('rangetype'),
			'doAJAXDeleteRangeType'
		));
		$this->registry->xajaxInterface->registerFunction(array(
			'LoadCategoryChildren',
			App::getModel('product'),
			'loadCategoryChildren'
		));
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->assign('datagrid_filter', App::getModel('rangetype')->getDatagridFilterData());
		$this->registry->template->display($this->loadTemplate('index.tpl'));
	}

	public function add ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'rangetype',
			'action' => '',
			'method' => 'post'
		));
		
		$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'required_data',
			'label' => $this->trans('TXT_MAIN_DATA')
		)));
		
		$languageData = $requiredData->AddChild(new FormEngine\Elements\FieldsetLanguage(Array(
			'name' => 'language_data',
			'label' => $this->trans('TXT_LANGUAGE_DATA')
		)));
		
		$languageData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'name',
			'label' => $this->trans('TXT_NAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME')),
				new FormEngine\Rules\LanguageUnique($this->trans('ERR_NAME_ALREADY_EXISTS'), 'rangetypetranslation', 'name')
			)
		)));
		
		$categoryData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'category_data',
			'label' => $this->trans('TXT_CATEGORY_DATA')
		)));
		
		$categoryData->AddChild(new FormEngine\Elements\Tree(Array(
			'name' => 'category',
			'label' => $this->trans('TXT_CATEGORY'),
			'choosable' => false,
			'selectable' => true,
			'sortable' => false,
			'clickable' => false,
			'items' => App::getModel('category')->getChildCategories(),
			'load_children' => Array(
				App::getModel('category'),
				'getChildCategories'
			)
		)));
		
		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			App::getModel('rangetype')->addNewRangeType($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/rangetype/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/rangetype');
			}
		}
		
		$this->registry->template->assign('form', $form->Render());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->display($this->loadTemplate('add.tpl'));
	}

	public function edit ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'rangetype',
			'action' => '',
			'method' => 'post'
		));
		
		$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'required_data',
			'label' => $this->trans('TXT_MAIN_DATA')
		)));
		
		$languageData = $requiredData->AddChild(new FormEngine\Elements\FieldsetLanguage(Array(
			'name' => 'language_data',
			'label' => $this->trans('TXT_LANGUAGE_DATA')
		)));
		
		$languageData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'name',
			'label' => $this->trans('TXT_NAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME')),
				new FormEngine\Rules\LanguageUnique($this->trans('ERR_NAME_ALREADY_EXISTS'), 'rangetypetranslation', 'name', null, Array(
					'column' => 'rangetypeid',
					'values' => (int) $this->registry->core->getParam()
				))
			)
		)));
		
		$categoryData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'category_data',
			'label' => $this->trans('TXT_CATEGORY_DATA')
		)));
		
		$rawRangetypeData = App::getModel('rangetype')->getRangeTypeView($this->registry->core->getParam());
		
		$categoryData->AddChild(new FormEngine\Elements\Tree(Array(
			'name' => 'category',
			'label' => $this->trans('TXT_CATEGORY'),
			'choosable' => false,
			'selectable' => true,
			'sortable' => false,
			'clickable' => false,
			'items' => App::getModel('category')->getChildCategories(0, $rawRangetypeData['rangetypecategorys']),
			'load_children' => Array(
				App::getModel('category'),
				'getChildCategories'
			)
		)));
		
		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		$rangetypeData = Array(
			'required_data' => Array(
				'language_data' => $rawRangetypeData['language']
			),
			'category_data' => Array(
				'category' => $rawRangetypeData['rangetypecategorys']
			)
		);
		
		$form->Populate($rangetypeData);
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				App::getModel('rangetype')->editRangeType($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->registry->core->getParam());
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/rangetype');
		}
		
		$this->registry->template->assign('form', $form->Render());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->display($this->loadTemplate('edit.tpl'));
	}
}