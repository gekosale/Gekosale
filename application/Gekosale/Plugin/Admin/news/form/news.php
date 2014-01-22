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

class NewsForm extends Component\Form
{
	
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'news',
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
			'name' => 'topic',
			'label' => $this->trans('TXT_TOPIC'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_TOPIC')),
				new FormEngine\Rules\LanguageUnique($this->trans('ERR_TOPIC_ALREADY_EXISTS'), 'newstranslation', 'topic', null, Array(
					'column' => 'newsid',
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
			'name' => 'summary',
			'label' => $this->trans('TXT_NEWS_SUMMARY')
		)));
		
		$languageData->AddChild(new FormEngine\Elements\RichTextEditor(Array(
			'name' => 'content',
			'label' => $this->trans('TXT_CONTENT'),
			'comment' => $this->trans('TXT_MAX_LENGTH') . ' 5000',
			'max_length' => 5000
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'publish',
			'label' => $this->trans('TXT_PUBLISH'),
			'default' => '1'
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'featured',
			'label' => $this->trans('Polecany'),
			'default' => '1'
		)));
		
		$metaData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'meta_data',
			'label' => $this->trans('TXT_META_INFORMATION')
		)));
		
		$metaData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p align="center">W przypadku braku informacji META system wygeneruje je automatycznie. W każdej chwili możesz je zmienić edytując dane poniżej.</p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));
		
		$additionalData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'additional_data',
			'label' => $this->trans('TXT_ADDITIONAL_INFORMATION')
		)));

		$additionalData->AddChild(new FormEngine\Elements\Date(Array(
			'name' => 'startdate',
			'label' => $this->trans('TXT_START_DATE')
		)));

		$additionalData->AddChild(new FormEngine\Elements\Date(Array(
			'name' => 'enddate',
			'label' => $this->trans('TXT_END_DATE')
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
			'comment' => $this->trans('TXT_KEYWORDS_HELP'),
		)));
		
		$photosPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'photos_pane',
			'label' => $this->trans('TXT_PHOTOS')
		)));
		
		$photosPane->AddChild(new FormEngine\Elements\Image(Array(
			'name' => 'photo',
			'label' => $this->trans('TXT_PHOTOS'),
			'repeat_min' => 0,
			'repeat_max' => FormEngine\FE::INFINITE,
			'upload_url' => App::getURLAdressWithAdminPane() . 'files/add',
			'main_id' => isset($this->populateData['photos_pane']['mainphotoid']) ? $this->populateData['photos_pane']['mainphotoid'] : NULL
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
		
		$Data = Event::dispatch($this, 'admin.news.initForm', Array(
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