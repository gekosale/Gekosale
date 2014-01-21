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

class StoreForm extends Component\Form
{
	
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'store',
			'action' => '',
			'method' => 'post'
		));
		
		$companyData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'company_data',
			'label' => $this->trans('TXT_COMPANY_DATA')
		)));
		
		$companyData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'companyname',
			'label' => $this->trans('TXT_COMPANY_NAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_COMPANYNAME'))
			)
		)));
		
		$companyData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'shortcompanyname',
			'label' => $this->trans('TXT_SHORT_COMPANY_NAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_SHORT_COMPANY_NAME'))
			)
		)));
		
		$companyData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'nip',
			'label' => $this->trans('TXT_NIP'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NIP')),
				new FormEngine\Rules\Custom($this->trans('ERR_WRONG_NIP'), Array(
					App::getModel('vat'),
					'checkVAT'
				))
			)
		)));
		
		$companyData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'krs',
			'label' => $this->trans('TXT_KRS')
		)));
		
		$addressData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'address_data',
			'label' => $this->trans('TXT_ADDRESS_COMPANY_DATA')
		)));
		
		$addressData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'placename',
			'label' => $this->trans('TXT_PLACENAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PLACE'))
			)
		)));
		
		$addressData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'postcode',
			'label' => $this->trans('TXT_POSTCODE'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_POSTCODE'))
			)
		)));
		
		$addressData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'street',
			'label' => $this->trans('TXT_STREET'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_STREET'))
			)
		)));
		
		$addressData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'streetno',
			'label' => $this->trans('TXT_STREETNO')
		)));
		
		$addressData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'placeno',
			'label' => $this->trans('TXT_PLACENO')
		)));
		
		$addressData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'province',
			'label' => $this->trans('TXT_PROVINCE'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PROVINCE'))
			)
		)));
		
		$addressData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'countries',
			'label' => $this->trans('TXT_NAME_OF_COUNTRY'),
			'options' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + App::getModel('countrieslist')->getCountryForSelect()),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME_OF_COUNTRY'))
			)
		)));
		
		$bankData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'bank_data',
			'label' => $this->trans('TXT_BANK_DATA')
		)));
		
		$bankData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'bankname',
			'label' => $this->trans('TXT_BANK_NAME')
		)));
		
		$bankData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'banknr',
			'label' => $this->trans('TXT_BANK_NUMBER'),
			'comment' => $this->trans('TXT_BANK_NUMBER_FORMAT'),
		)));
		
		$photosPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'photos_pane',
			'label' => $this->trans('TXT_SINGLE_PHOTO')
		)));
		
		$photosPane->AddChild(new FormEngine\Elements\Image(Array(
			'name' => 'photo',
			'label' => $this->trans('INVOICE_SINGLE_PHOTO'),
			'repeat_min' => 0,
			'repeat_max' => 1,
			'upload_url' => App::getURLAdressWithAdminPane() . 'files/add'
		)));
		
		$invoicedata = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'invoice_data',
			'label' => $this->trans('TXT_INVOICE')
		)));
		
		$isinvoiceshopslogan = $invoicedata->AddChild(new FormEngine\Elements\RadioValueGroup(Array(
			'name' => 'isinvoiceshopslogan',
			'label' => $this->trans('TXT_INVOICE_SHOW_SHOP_NAME_AND_TAG'),
			'options' => FormEngine\Option::Make(Array(
				'1' => $this->trans('TXT_INVOICE_SHOW_SHOP_NAME'),
				'2' => $this->trans('TXT_INVOICE_SHOW_SHOP_NAME_AND_TAG')
			)),
			'value' => '1'
		)));
		
		$invoicedata->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'invoiceshopslogan',
			'label' => $this->trans('TXT_NAME_OF_INVOICE_TAG'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('TXT_EMPTY_NAME_OF_INVOICE_TAG'))
			),
			'dependencies' => Array(
				new FormEngine\Dependency(FormEngine\Dependency::HIDE, $isinvoiceshopslogan, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('2')))
			)
		)));
		
		$Data = Event::dispatch($this, 'admin.store.initForm', Array(
			'form' => $form,
			'id' => (int) $this->registry->core->getParam(),
			'data' => $this->populateData
		));
		
		if (! empty($Data)){
			$form->Populate($Data);
		}
		
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		return $form;
	}
}