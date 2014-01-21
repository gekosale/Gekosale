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

class AccountForm extends Component\Form
{
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'account',
			'action' => '',
			'method' => 'post',
			'tabs' => 1
		));
		
		$billingData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'billing_data',
			'label' => 'Dane abonenta'
		)));
		
		if ($this->populateData['billing_data']['verified'] == 0){
			$billingData->AddChild(new FormEngine\Elements\Tip(Array(
				'tip' => '<p>Twoje konto w systemie WellCommerce nie zostało jeszcze zweryfikowane. Możesz korzystać ze wszystkich funkcji oprogramowania, z wyłączeniem automatycznej integracji z systemami płatności.</p>'
			)));
		}
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'firstname',
			'label' => $this->trans('TXT_FIRSTNAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_FIRSTNAME'))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'surname',
			'label' => $this->trans('TXT_SURNAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_SURNAME'))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'street',
			'label' => $this->trans('TXT_STREET'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_STREET'))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'streetno',
			'label' => $this->trans('TXT_STREETNO'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_STREETNO'))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'placeno',
			'label' => $this->trans('TXT_PLACENO')
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'city',
			'label' => $this->trans('TXT_PLACE'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PLACE'))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'postcode',
			'label' => $this->trans('TXT_POSTCODE'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_POSTCODE')),
				new FormEngine\Rules\Format($this->trans('ERR_WRONG_FORMAT'), "/[0-9]{2}\-[0-9]{3}/")
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'countryid',
			'label' => $this->trans('TXT_NAME_OF_COUNTRY'),
			'options' => FormEngine\Option::Make(App::getModel('countrieslist')->getCountryForSelect()),
			'default' => $this->registry->loader->getParam('countryid'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME_OF_COUNTRY'))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'companyname',
			'label' => $this->trans('TXT_COMPANYNAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_COMPANYNAME'))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'nip',
			'label' => $this->trans('TXT_NIP'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NIP'))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'regon',
			'label' => $this->trans('TXT_REGON'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_REGON'))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'email',
			'label' => $this->trans('TXT_EMAIL'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_EMAIL')),
				new FormEngine\Rules\Email($this->trans('ERR_WRONG_EMAIL'))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'phone',
			'label' => $this->trans('TXT_PHONE'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PHONE'))
			)
		)));
		
		$Data = Event::dispatch($this, 'admin.instancemanager.initForm', Array(
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