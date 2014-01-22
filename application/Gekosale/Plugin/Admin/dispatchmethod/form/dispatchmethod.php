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

class DispatchmethodForm extends Component\Form
{
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'dispatchmethod',
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
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME')),
				new FormEngine\Rules\Unique($this->trans('ERR_NAME_ALREADY_EXISTS'), 'dispatchmethod', 'name', null, Array(
					'column' => 'iddispatchmethod',
					'values' => (int) $this->registry->core->getParam()
				))
			)
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\MultiSelect(Array(
			'name' => 'paymentmethodname',
			'label' => $this->trans('TXT_PAYMENTMETHOD'),
			'options' => FormEngine\Option::Make(App::getModel('paymentmethod')->getPaymentmethodToSelect())
		)));
		
		$type = $requiredData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'type',
			'label' => 'Obliczanie kosztów',
			'options' => Array(
				new FormEngine\Option('1', 'Koszt zależny od sumy zamówienia'),
				new FormEngine\Option('2', 'Koszt zależny od wagi')
			),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_DELIVERY_COST_TYPE'))
			),
			'default' => ''
		)));
		
		$currencies = App::getModel('currencieslist')->getCurrencyForSelect();
		
		$currency = $requiredData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'currencyid',
			'label' => $this->trans('TXT_CURRENCY_DATA'),
			'options' => FormEngine\Option::Make($currencies),
			'default' => App::getContainer()->get('session')->getActiveShopCurrencyId()
		)));
		
		$dispatchmethodprice = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'dispatchmethod_data',
			'label' => $this->trans('TXT_DISPATCHMETHOD_PRICE')
		)));
		
		$dispatchmethodprice->AddChild(new FormEngine\Elements\RangeEditor(Array(
			'name' => 'table',
			'label' => $this->trans('TXT_DISPATCHMETHOD_TABLE_PRICE'),
			'suffix' => $this->trans('TXT_CURRENCY'),
			'range_suffix' => $this->trans('TXT_CURRENCY'),
			'allow_vat' => true,
			'range_precision' => 4,
			'price_precision' => 4
		)));
		
		$dispatchmethodprice->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'maximumweight',
			'label' => $this->trans('TXT_MAXIMUM_WEIGHT'),
			'comment' => $this->trans('TXT_MAXIMUM_WEIGHT_HELP'),
			'suffix' => $this->trans('TXT_KG')
		)));
		
		$dispatchmethodprice->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $type, new FormEngine\Conditions\Equals(1)));
		
		$dispatchmethodweight = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'dispatchmethodweight_data',
			'label' => $this->trans('TXT_DISPATCHMETHOD_WEIGHT_PRICE')
		)));
		
		$dispatchmethodweight->AddChild(new FormEngine\Elements\RangeEditor(Array(
			'name' => 'tableweight',
			'label' => $this->trans('TXT_DISPATCHMETHOD_WEIGHT_TABLE_PRICE'),
			'suffix' => $this->trans('TXT_CURRENCY'),
			'range_suffix' => $this->trans('TXT_KG'),
			'allow_vat' => true
		)));
		
		$dispatchmethodweight->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'freedelivery',
			'label' => $this->trans('TXT_FREE_DELIVERY'),
			'comment' => $this->trans('TXT_FREE_DELIVERY_HELP')
		)));
		
		$dispatchmethodweight->AddDependency(new FormEngine\Dependency(FormEngine\Dependency::SHOW, $type, new FormEngine\Conditions\Equals(2)));
		
		$countryPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'country_pane',
			'label' => $this->trans('TXT_COUNTRY')
		)));
		
		$countryPane->AddChild(new FormEngine\Elements\MultiSelect(Array(
			'name' => 'countryids',
			'label' => $this->trans('TXT_COUNTRY'),
			'options' => FormEngine\Option::Make(App::getModel('countrieslist')->getCountryForSelect())
		)));
		
		$descriptionData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'description_data',
			'label' => $this->trans('TXT_DESCRIPTION_COURIER')
		)));
		
		$descriptionData->AddChild(new FormEngine\Elements\RichTextEditor(Array(
			'name' => 'description',
			'label' => $this->trans('TXT_DESCRIPTION'),
			'comment' => $this->trans('TXT_MAX_LENGTH') . ' 5000',
			'max_length' => 5000
		)));
		
		$photosPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'photos_pane',
			'label' => $this->trans('TXT_PHOTOS')
		)));
		
		$photosPane->AddChild(new FormEngine\Elements\Image(Array(
			'name' => 'photo',
			'label' => $this->trans('TXT_PHOTOS'),
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
		
		$Data = Event::dispatch($this, 'admin.dispatchmethod.initForm', Array(
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