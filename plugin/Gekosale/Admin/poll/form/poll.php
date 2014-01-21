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

class PollForm extends Component\Form
{
	
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'poll',
			'action' => '',
			'method' => 'post'
		));
		
		$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'required_data',
			'label' => $this->trans('TXT_MAIN_DATA')
		)));
		
		$langData = $requiredData->AddChild(new FormEngine\Elements\FieldsetLanguage(Array(
			'name' => 'lang_data',
			'label' => $this->trans('TXT_LANGUAGE_DATA')
		)));
		
		$langData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'questions',
			'label' => $this->trans('TXT_QUESTIONS'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_QUESTIONS'))
			)
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'publish',
			'label' => $this->trans('TXT_PUBLISH'),
			'default' => '1'
		)));
		
		$answers = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'answers_book',
			'label' => $this->trans('TXT_ANSWERS_DATA')
		)));
		
		$answersData = $answers->AddChild(new FormEngine\Elements\FieldsetRepeatable(Array(
			'name' => 'answers_data',
			'label' => $this->trans('TXT_ANSWERS_DATA'),
			'repeat_min' => 1,
			'repeat_max' => FormEngine\FE::INFINITE
		)));
		
		$languages = App::getModel('language')->getLanguageALL();
		foreach ($languages as $language){
			$answersData->AddChild(new FormEngine\Elements\TextField(Array(
				'name' => 'name_' . $language['id'],
				'label' => $this->trans('TXT_ANSWERS'),
				'rules' => Array(
					new FormEngine\Rules\Required($this->trans('ERR_EMPTY_ANSWERS'))
				),
				'suffix' => '<img src="' . DESIGNPATH . '/_images_common/icons/languages/' . $language['flag'] . '" />'
			)));
		}
		
		$layerData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'view_data',
			'label' => $this->trans('TXT_STORES')
		)));
		
		$layerData->AddChild(new FormEngine\Elements\LayerSelector(Array(
			'name' => 'view',
			'label' => $this->trans('TXT_VIEW'),
			'default' => Helper::getViewIdsDefault()
		)));
		
		$Data = Event::dispatch($this, 'admin.poll.initForm', Array(
			'form' => $form,
			'id' => (int) $this->registry->core->getParam(),
			'data' => $this->populateData
		));
		
		if (! empty($Data)){
			$form->Populate($Data);
		}
		
		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		return $form;
	}

}