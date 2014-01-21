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

class SubstitutedServiceForm extends Component\Form
{
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'substitutedservice',
			'action' => '',
			'method' => 'post'
		));

		$mainData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'main_data',
			'label' => $this->trans('TXT_MAIN_OPTIONS')
		)));

		$mainData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Aby zarządzać szablonami, przejdź do <a href="' . $this->registry->router->generate('admin', true, Array(
				'controller' => 'templateeditor',
				'action' => 'edit',
				'param' => $this->registry->loader->getParam('theme')
			)) . '" target="_blank">Szablony stylów &raquo; Biblioteka szablonów</a>, następnie rozwiń gałąź <b>templates</b> &raquo; <b>email</b>.</p>'
		)));

		$mainData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'name',
			'label' => $this->trans('TXT_NAME') . '/ ' . $this->trans('TXT_MAIL_TITLE'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME')),
				new FormEngine\Rules\Unique($this->trans('ERR_NAME_ALREADY_EXISTS'), 'substitutedservice', 'name', null, Array(
					'column' => 'idsubstitutedservice',
					'values' => (int) $this->registry->core->getParam()
				))
			)
		)));

		$mainData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p align="center"><strong>Określ zdarzenie oraz zakres czasowy</strong> <br />
						</p>',
			'direction' => FormEngine\Elements\Tip::DOWN,
			'short_tip' => '<p align="center"><strong>Określ zdarzenie oraz zakres czasowy</strong></p>'
		)));

		$periods = App::getModel('substitutedservice')->getPeriodsAllToSelect();

		$mainData->AddChild(new FormEngine\Elements\RadioValueGroup(Array(
			'name' => 'actionid',
			'label' => $this->trans('TXT_ACTION'),
			'options' => Array(
				new FormEngine\Option('1', 'Klient zarejestrował się i przez %select% nie złożył zamówienia'),
				new FormEngine\Option('2', 'Klient nie logował się w sklepie od... %date%'),
				new FormEngine\Option('3', 'Ostatnie logowanie klienta było %select% temu'),
				new FormEngine\Option('4', 'Klient nie dokonał płatności online za zamówienie przez %select% od daty jego złożenia'),
				new FormEngine\Option('5', 'Klient nie potwierdził zamówienia przez %select% od daty jego złożenia')
			),
			'suboptions' => Array(
				'1' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + $periods),
				'3' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + $periods),
				'4' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + $periods),
				'5' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + $periods)
			)
		)));

		$mainData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p align="center">Zaznacz checkbox, jeśli chcesz, by administrator
							dostał kopię wiadomości, która zostanie wysłana do klienta.</p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));

		$mainData->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'admin',
			'label' => $this->trans('TXT_ADMIN')
		)));

		$Data = Event::dispatch($this, 'admin.substitutedservice.initForm', Array(
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