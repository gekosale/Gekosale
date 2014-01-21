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

class CollectionForm extends Component\Form
{
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'collection',
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
				new FormEngine\Rules\LanguageUnique($this->trans('ERR_NAME_ALREADY_EXISTS'), 'collectiontranslation', 'name', null, Array(
					'column' => 'collectionid',
					'values' => (int) $this->registry->core->getParam()
				))
			)
		)));
		
		$languageData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'seo',
			'label' => $this->trans('TXT_SEO_URL'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_SEO_URL'))
			)
		)));
		
		$languageData->AddChild(new FormEngine\Elements\RichTextEditor(Array(
			'name' => 'description',
			'label' => $this->trans('TXT_DESCRIPTION'),
			'rows' => 10
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'producerid',
			'label' => $this->trans('TXT_PRODUCER'),
			'options' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + App::getModel('producer')->getProducerToSelect()),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PRODUCER')),
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
			'name' => 'keyword_title',
			'label' => $this->trans('TXT_KEYWORD_TITLE')
		)));
		
		$languageData->AddChild(new FormEngine\Elements\Textarea(Array(
			'name' => 'keyword_description',
			'label' => $this->trans('TXT_KEYWORD_DESCRIPTION'),
			'comment' => $this->trans('TXT_MAX_LENGTH') . ' 1000',
			'max_length' => 1000
		)));
		
		$languageData->AddChild(new FormEngine\Elements\Textarea(Array(
			'name' => 'keyword',
			'label' => $this->trans('TXT_KEYWORDS'),
			'comment' => $this->trans('TXT_KEYWORDS_HELP')
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
			'label' => $this->trans('TXT_VIEW'),
			'default' => Helper::getViewIdsDefault()
		)));
		
		$Data = Event::dispatch($this, 'admin.collection.initForm', Array(
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