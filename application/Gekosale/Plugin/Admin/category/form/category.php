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
namespace Gekosale\Plugin;

use FormEngine;

class CategoryForm extends Component\Form
{

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'category',
			'action' => '',
			'method' => 'post'
		));
		
		$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'required_data',
			'label' => $this->trans('TXT_BASIC_INFORMATION')
		)));
		
		$languageData = $requiredData->AddChild(new FormEngine\Elements\FieldsetLanguage(Array(
			'name' => 'language_data',
			'label' => $this->trans('TXT_LANGUAGE_DATA')
		)));
		
		$languageData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'name',
			'label' => $this->trans('TXT_NAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME'))
			)
		)));
		
		$languageData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'seo',
			'label' => $this->trans('TXT_SEO_URL'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_CATEGORY_SEO')),
				new FormEngine\Rules\Format($this->trans('ERR_ALPHANUMERIC_INVALID'), '/^[A-Za-z0-9-_\/\",\'\s]+$/'),
				new FormEngine\Rules\LanguageUnique($this->trans('ERR_CATEGORY_SEO_ALREADY_EXISTS'), 'categorytranslation', 'seo', null, Array(
					'column' => 'categoryid',
					'values' => (int) $this->registry->core->getParam()
				))
			)
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'enable',
			'label' => $this->trans('TXT_ENABLE_CATEGORY'),
			'default' => '1'
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'distinction',
			'label' => $this->trans('TXT_CATEGORY_ORDER')
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\StaticText(Array(
			'text' => '<p>' . $this->trans('TXT_PARENT_CATEGORY') . '</p>'
		)));
		
		if ($this->populateData['required_data']['categoryid']){
			$active = $this->populateData['required_data']['categoryid'];
		}
		else{
			$active = $this->registry->core->getParam();
		}
		
		$requiredData->AddChild(new FormEngine\Elements\Tree(Array(
			'name' => 'categoryid',
			'label' => $this->trans('TXT_CATEGORY'),
			'comment' => $this->trans('TXT_PARENT_CATEGORY_EXAMPLE'),
			'choosable' => true,
			'selectable' => false,
			'sortable' => false,
			'clickable' => false,
			'items' => App::getModel('category')->getChildCategories(0, Array(
				$active
			)),
			'restrict' => (int) $this->registry->core->getParam(),
			'load_children' => Array(
				App::getModel('category'),
				'getChildCategories'
			),
			'rules' => Array(
				new FormEngine\Rules\Custom($this->trans('ERR_BIND_SELF_PARENT_INVALID'), Array(
					App::getModel('category'),
					'checkParentValue'
				), Array(
					'categoryid' => (int) $this->registry->core->getParam()
				))
			)
		)));
		
		$metaData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'meta_data',
			'label' => $this->trans('TXT_META_INFORMATION')
		)));
		
		$metaData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p align="center">W przypadku braku informacji META system wygeneruje je automatycznie. W każdej chwili możesz je zmienić edytując dane poniżej.</p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));
		
		$languageData = $metaData->AddChild(new FormEngine\Elements\FieldsetLanguage(Array(
			'name' => 'language_data',
			'label' => $this->trans('TXT_LANGUAGE_DATA')
		)));
		
		$languageData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'keywordtitle',
			'label' => $this->trans('TXT_KEYWORD_TITLE')
		)));
		
		$languageData->AddChild(new FormEngine\Elements\Textarea(Array(
			'name' => 'keyworddescription',
			'label' => $this->trans('TXT_KEYWORD_DESCRIPTION'),
			'comment' => $this->trans('TXT_MAX_LENGTH') . ' 1000',
			'max_length' => 1000
		)));
		
		$languageData->AddChild(new FormEngine\Elements\Textarea(Array(
			'name' => 'keyword',
			'label' => $this->trans('TXT_KEYWORDS'),
			'comment' => $this->trans('TXT_KEYWORDS_HELP'),
			'max_length' => 1000
		)));
		
		$descriptionPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'description_pane',
			'label' => $this->trans('TXT_DESCRIPTION')
		)));
		
		$descriptionLanguageData = $descriptionPane->AddChild(new FormEngine\Elements\FieldsetLanguage(Array(
			'name' => 'language_data',
			'label' => $this->trans('TXT_LANGUAGE_DATA')
		)));
		
		$descriptionLanguageData->AddChild(new FormEngine\Elements\RichTextEditor(Array(
			'name' => 'shortdescription',
			'label' => $this->trans('TXT_SHORTDESCRIPTION'),
			'comment' => $this->trans('TXT_MAX_LENGTH') . ' 1000',
			'max_length' => 3000,
			'rows' => 20
		)));
		
		$descriptionLanguageData->AddChild(new FormEngine\Elements\RichTextEditor(Array(
			'name' => 'description',
			'label' => $this->trans('TXT_DESCRIPTION'),
			'comment' => $this->trans('TXT_MAX_LENGTH') . ' 5000',
			'max_length' => 3000,
			'rows' => 30
		)));
		
		$products = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'category_products',
			'label' => $this->trans('TXT_PRODUCTS')
		)));
		
		$products->AddChild(new FormEngine\Elements\ProductSelect(Array(
			'name' => 'products',
			'label' => $this->trans('TXT_PRODUCTS'),
			'repeat_min' => 1,
			'repeat_max' => FormEngine\FE::INFINITE
		)));
		
		$photosPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'photos_pane',
			'label' => $this->trans('TXT_SINGLE_PHOTO')
		)));
		
		$photosPane->AddChild(new FormEngine\Elements\Image(Array(
			'name' => 'photo',
			'label' => $this->trans('TXT_SINGLE_PHOTO'),
			'repeat_min' => 0,
			'repeat_max' => 1,
			'upload_url' => App::getURLAdressWithAdminPane() . 'files/add'
		)));
		
		$layerData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'view_data',
			'label' => $this->trans('TXT_STORES')
		)));
		
		$layerData->AddChild(new FormEngine\Elements\LayerSelector(Array(
			'name' => 'view',
			'label' => $this->trans('TXT_VIEW')
		)));
		
		$Data = Event::dispatch($this, 'admin.category.initForm', Array(
			'form' => $form,
			'id' => (int) $this->registry->core->getParam(),
			'data' => $this->populateData
		));
		
		if (! empty($Data)){
			$form->Populate($Data);
		}
		
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		return $form;
	}
}