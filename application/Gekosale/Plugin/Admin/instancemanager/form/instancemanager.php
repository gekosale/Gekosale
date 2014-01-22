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

class InstanceManagerForm extends Component\Form
{
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$instance = new Instance();
		
		$limits = $instance->getLimits();
		
		$currentLimits = $instance->getCurrentLimits();
		
		$mainInfo = $instance->getInstanceMainInfo();
		
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'globalsettings',
			'action' => '',
			'method' => 'post',
			'tabs' => 1
		));
		
		$mainData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'main_data',
			'label' => 'Informacje podstawowe'
		)));
		
		$billable = ($mainInfo['result']['info']['billable']) ? 'Tak' : 'Nie';
		$lastbilled = ($mainInfo['result']['info']['lastbilled'] === NULL) ? 'Brak' : $mainInfo['result']['info']['lastbilled'];
		
		$mainData->AddChild(new FormEngine\Elements\StaticText(Array(
		'text' => "
				<ul style=\"list-style:none;\">
					<li style=\"margin: 10px;\"><strong>Nazwa instancji:</strong> {$mainInfo['result']['info']['name']}</li>
					<li style=\"margin: 10px;\"><strong>Data założenia:</strong> {$mainInfo['result']['info']['adddate']}</li>
					<li style=\"margin: 10px;\"><strong>Wersja płatna:</strong> {$billable}</li>
					<li style=\"margin: 10px;\"><strong>Ostatnia płatność:</strong> {$lastbilled}</li>
					<li style=\"margin: 10px;\"><strong>Rozpoczęcie abonamentu:</strong> {$mainInfo['result']['info']['billedfrom']}</li>
					<li style=\"margin: 10px;\"><strong>Domena główna:</strong> {$mainInfo['result']['info']['domainname']}</li>
					<li style=\"margin: 10px;\"><strong>Pakiet abonamentowy:</strong> {$mainInfo['result']['info']['product']}</li>
				</ul>"
		)));
		
		$billingData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'billing_data',
			'label' => $this->trans('TXT_EDIT_ORDER_BILLING_DATA')
		)));
		
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
			'label' => $this->trans('TXT_COMPANYNAME')
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'nip',
			'label' => $this->trans('TXT_NIP'),
			'rules' => Array(
				new FormEngine\Rules\Custom($this->trans('ERR_WRONG_NIP'), Array(
					App::getModel('vat'),
					'checkVAT'
				))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'email',
			'label' => $this->trans('TXT_EMAIL'),
			'comment' => $this->trans('TXT_EMAIL_FORM'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_EMAIL')),
				new FormEngine\Rules\Email($this->trans('ERR_WRONG_EMAIL'))
			)
		)));
		
		$paymentData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'invoice_data',
			'label' => 'Płatności'
		)));
		
		$limitsData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'config_data',
			'label' => 'Limity'
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p align="center">Limit produktów oznacza maksymalną ilość produktów jaką możesz posiadać we wszystkich swoich sklepach, w ramach wykupionego pakietu abonamentowego.</p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\ProgressBar(Array(
			'name' => 'products',
			'label' => 'Produkty',
			'total' => $limits['result']['limits']['products'],
			'completed' => $currentLimits['products']
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p align="center">Limit produktów oznacza maksymalną ilość produktów jaką możesz posiadać we wszystkich swoich sklepach, w ramach wykupionego pakietu abonamentowego.</p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\ProgressBar(Array(
			'name' => 'categories',
			'label' => 'Kategorie',
			'total' => $limits['result']['limits']['categories'],
			'completed' => $currentLimits['categories']
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p align="center">Limit zamówień oznacza maksymalną ilość zamówień miesięcznie jakie mogą zostać złożone przez klientów lub administratorów we wszystkich Twoich sklepach.</p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\ProgressBar(Array(
			'name' => 'orders',
			'label' => 'Zamówienia (mc)',
			'total' => $limits['result']['limits']['orders'],
			'completed' => $currentLimits['orders']
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p align="center">Limit klientów oznacza maksymalną ilość nowych kont klientów miesięcznie we wszystkich sklepach. Do limitu nie są wliczani klienci, którzy złożyli zamówienia bez rejestracji.</p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\ProgressBar(Array(
			'name' => 'clients',
			'label' => 'Klienci (mc)',
			'total' => $limits['result']['limits']['clients'],
			'completed' => $currentLimits['clients']
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p align="center">Limit użytkowników oznacza maksymalną ilość administratorów, którzy mogą obsługiwać wszystkie sklepy, w ramach wykupionego pakietu abonamentowego.</p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\ProgressBar(Array(
			'name' => 'users',
			'label' => 'Użytkownicy',
			'total' => $limits['result']['limits']['users'],
			'completed' => $currentLimits['users']
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p align="center">Limit sklepów oznacza maksymalną ilość sklepów jakie możesz uruchomić w ramach wykupionego pakietu abonamentowego.</p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));
		
		$limitsData->AddChild(new FormEngine\Elements\ProgressBar(Array(
			'name' => 'views',
			'label' => 'Sklepy',
			'total' => $limits['result']['limits']['views'],
			'completed' => $currentLimits['views']
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