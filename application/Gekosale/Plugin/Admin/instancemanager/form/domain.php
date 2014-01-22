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

class DomainForm extends Component\Form
{
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'domain',
			'action' => '',
			'method' => 'post'
		));

		$domainData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'add_domain',
			'label' => 'Dodawanie domeny'
		)));

		$domainData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p align="left">Możesz dodać bezpłatnie dowolną ilość adresów w domenie *.wellcommerce.pl. Możesz też dodać swoje domeny - ale wcześniej musisz przekierować je na nasz DNS. <a href="http://wellcommerce.pl/zasoby/hosting/jak-dodac-wlasna-domene/" target="_blank">Zobacz instrukcję</a>.</p>'
		)));

		$domainData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'domain',
			'label' => 'Nazwa domeny',
			'comment' => 'Nazwa samej domeny bez http://',
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_URL')),
				new FormEngine\Rules\Custom($this->trans('Ten adres jest niepoprawny lub zajęty.'), Array(
					$this,
					'checkDomain'
				))
			)
		)));

		$domainListData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'list_domain',
			'label' => 'Lista domen'
		)));

		$domainListData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p align="left">Poniżej znajduje się lista domen dostępnych dla Twoich sklepów. Jeżeli chcesz usunąć domeny, skontaktuj się z nami za pomocą <a href="http://pomoc.wellcommerce.pl/" target="_blank">formularza zgłoszeniowego</a>.</p>'
		)));

		$html = '
			<div class="invoice-list">
			<table cellpadding="0" cellspacing="0" style="margin-bottom: 30px;">
				<thead>
	   				<tr>
	     				<th>Nazwa</th>
	      				<th>Data ważności</th>
	      				<th>Name Server 1</th>
	      				<th>Name Server 2</th>
	   				</tr>
				</thead>
			<tbody>';

		$this->instance = new Instance();
		$result = $this->instance->getDomainsForInstance();
		foreach ($result['result'] as $domain){

			$html .= "<tr>";
			$html .= "<td>{$domain['name']}</td>";
			$html .= "<td>{$domain['expires']}</td>";
			$html .= "<td>{$domain['ns1']}</td>";
			$html .= "<td>{$domain['ns2']}</td>";
			$html .= "</tr>";
		}

		$domainListData->AddChild(new FormEngine\Elements\StaticText(Array(
			'text' => $html
		)));

		if (! empty($this->populateData)){
			$form->Populate($this->populateData);
		}

		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());

		return $form;
	}

	public function checkDomain ($params)
	{
		$instance = new Instance();
		$result = $instance->domainCheck($params);
		if (isset($result['result']['valid']) && $result['result']['valid'] == 1){
			return true;
		}
		return false;
	}
}