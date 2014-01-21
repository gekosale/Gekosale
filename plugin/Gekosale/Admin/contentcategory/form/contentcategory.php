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
namespace Gekosale;

use FormEngine;

class ContentCategoryForm extends Component\Form
{
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'contentcategory',
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
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME'))
			)
		)));

		$languageData->AddChild(new FormEngine\Elements\RichTextEditor(Array(
			'name' => 'description',
			'label' => $this->trans('TXT_CONTENT')
		)));

		$requiredData->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'header',
			'label' => $this->trans('TXT_ENABLE_IN_HEADER'),
			'default' => '1'
		)));

		$requiredData->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'footer',
			'label' => $this->trans('TXT_ENABLE_IN_FOOTER'),
			'default' => '1'
		)));

		$requiredData->AddChild(new FormEngine\Elements\Tip(Array(
			'direction' => FormEngine\Elements\Tip::DOWN,
			'tip' => '<p>' . $this->trans('TXT_PARENT_CATEGORY_EXAMPLE') . '</p>'
		)));

		$requiredData->AddChild(new FormEngine\Elements\Tree(Array(
			'name' => 'contentcategoryid',
			'label' => $this->trans('TXT_CATEGORY'),
			'choosable' => true,
			'selectable' => false,
			'sortable' => false,
			'clickable' => false,
			'restrict' => (int) $this->registry->core->getParam(),
			'items' => App::getModel('contentcategory')->getContentCategoryALL($this->registry->core->getParam())
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
			'name' => 'keyword_title',
			'label' => $this->trans('TXT_KEYWORD_TITLE')
		)));

		$languageData->AddChild(new FormEngine\Elements\Textarea(Array(
			'name' => 'keyword_description',
			'label' => $this->trans('TXT_KEYWORD_DESCRIPTION')
		)));

		$languageData->AddChild(new FormEngine\Elements\Textarea(Array(
			'name' => 'keyword',
			'label' => $this->trans('TXT_KEYWORDS'),
			'comment' => $this->trans('TXT_KEYWORDS_HELP'),
		)));

		$redirectData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'redirect_data',
			'label' => $this->trans('TXT_CONTENTCATEGORY_REDIRECT')
		)));

		$redirect = $redirectData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'redirect',
			'label' => $this->trans('TXT_REDIRECT_TYPE'),
			'options' => Array(
				new FormEngine\Option(0, $this->trans('TXT_REDIRECT_NONE')),
				new FormEngine\Option(1, $this->trans('TXT_REDIRECT_INTERNAL')),
				new FormEngine\Option(2, $this->trans('TXT_REDIRECT_EXTERNAL'))
			),
			'default' => 1
		)));

		$redirectData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'redirect_route',
			'label' => $this->trans('TXT_REDIRECT_INTERNAL_URL'),
			'options' => Array(
				new FormEngine\Option('frontend.home', $this->trans('TXT_CONTROLLER_MAINSIDE')),
				new FormEngine\Option('frontend.conditions', $this->trans('TXT_CONDITIONS')),
				new FormEngine\Option('frontend.clientlogin', $this->trans('TXT_CONTROLLER_CLIENTLOGIN')),
				new FormEngine\Option('frontend.registration', $this->trans('TXT_CONTROLLER_REGISTRATION')),
				new FormEngine\Option('frontend.contact', $this->trans('TXT_CONTROLLER_CONTACT')),
				new FormEngine\Option('frontend.sitemap', $this->trans('TXT_CONTROLLER_SITEMAP')),
				new FormEngine\Option('frontend.news', $this->trans('TXT_CONTROLLER_NEWS')),
				new FormEngine\Option('frontend.forgotpassword', $this->trans('TXT_CONTROLLER_FORGOTPASSWORD')),
				new FormEngine\Option('frontend.producerlist', $this->trans('TXT_CONTROLLER_PRODUCERLIST')),
				new FormEngine\Option('frontend.categorylist', $this->trans('TXT_CONTROLLER_CATEGORYLIST')),
			),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::SHOW, $redirect, new FormEngine\Conditions\Equals(1))
			),
			'default' => 1
		)));

		$redirectData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'redirect_url',
			'label' => $this->trans('TXT_REDIRECT_EXTERNAL_URL'),
			'comment' => 'Adres poprzedzony	 http:// lub https://',
			'rules' => Array(
				new FormEngine\Rules\Format($this->trans('ERR_WRONG_FORMAT'), '~^https?://~')
			),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::SHOW, $redirect, new FormEngine\Conditions\Equals(2))
			)
		)));

		$layerData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'view_data',
			'label' => $this->trans('TXT_STORES')
		)));

		$layerData->AddChild(new FormEngine\Elements\LayerSelector(Array(
			'name' => 'view',
			'label' => $this->trans('TXT_VIEW'),
			'default' => Helper::getViewIdsDefault()
		)));

		$Data = Event::dispatch($this, 'admin.contentcategory.initForm', Array(
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