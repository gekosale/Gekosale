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

class LanguageForm extends Component\Form
{
	
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'language',
			'action' => '',
			'method' => 'post'
		));
		
		$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'required_data',
			'label' => $this->trans('TXT_MAIN_DATA')
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'name',
			'label' => $this->trans('TXT_NAME'),
			'comment' => $this->trans('TXT_EXAMPLE') . ': en_EN',
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME')),
				new FormEngine\Rules\Unique($this->trans('ERR_NAME_ALREADY_EXISTS'), 'language', 'name', null, Array(
					'column' => 'idlanguage',
					'values' => (int) $this->registry->core->getParam()
				))
			)
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'translation',
			'label' => $this->trans('TXT_TRANSLATION'),
			'comment' => $this->trans('TXT_EXAMPLE') . ': TXT_ENGLISH',
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_TRANSLATION'))
			)
		)));
		
		if ($this->registry->core->getParam() == ''){
			$requiredData->AddChild(new FormEngine\Elements\Select(Array(
				'name' => 'copylanguage',
				'label' => $this->trans('TXT_COPY_FROM_LANGUAGE'),
				'options' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + App::getModel('language')->getLanguageALLToSelect()),
				'default' => 0
			)));
		}
		
		$requiredData->AddChild(new FormEngine\Elements\LocalFile(Array(
			'name' => 'translations',
			'label' => 'Plik z tłumaczeniem',
			'file_source' => 'upload/',
			'file_types' => Array(
				'xml'
			)
		)));
		
		$currencies = App::getModel('currencieslist')->getCurrencyForSelect();
		
		$currencyData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'currency_data',
			'label' => $this->trans('TXT_CURRENCY_DATA')
		)));
		
		$currencyData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'currencyid',
			'label' => $this->trans('TXT_DEFAULT_LANGUAGE_CURRENCY'),
			'options' => FormEngine\Option::Make($currencies)
		)));
		
		$flagPane = $form->addChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'flag_pane',
			'label' => $this->trans('TXT_LANGUAGE_FLAG')
		)));
		
		$flagPane->AddChild(new FormEngine\Elements\LocalFile(Array(
			'name' => 'flag',
			'label' => $this->trans('TXT_LANGUAGE_FLAG'),
			'file_source' => 'design/_images_common/icons/languages/',
			'file_types' => Array(
				'png'
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
		
		$Data = Event::dispatch($this, 'admin.language.initForm', Array(
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