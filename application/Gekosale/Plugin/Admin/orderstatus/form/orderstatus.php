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

class OrderStatusForm extends Component\Form
{
	
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'orderstatus',
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
				new FormEngine\Rules\LanguageUnique($this->trans('ERR_NAME_ALREADY_EXISTS'), 'orderstatustranslation', 'name', null, Array(
					'column' => 'orderstatusid',
					'values' => (int) $this->registry->core->getParam()
				))
			)
		)));
		
		$languageData->AddChild(new FormEngine\Elements\Textarea(Array(
			'name' => 'comment',
			'label' => $this->trans('TXT_DEFAULT_ORDER_COMMENT')
		)));
		
		$languageData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Podaj domyślną treść wiadomości SMS jaka zostanie wysłana do użytkownika. możliwe jest stosowanie znaczników:</p>
			<ul>
				<li><strong>{ORDER_ID}</strong> - numer zamówienia</li>
				<li><strong>{ORDER_DATE}</strong> - data zamówienia</li>
				<li><strong>{ORDER_STATUS}</strong> - status zamówienia</li>
				<li><strong>{ORDER_CLIENT}</strong> - imię i nazwisko lub nazwa firmy</li>
			</ul>
			<p><strong>Pamiętaj aby nie była dłuższa niż 160 znaków</strong>. Polskie akcenty zostaną automatycznie usunięte w trakcie wysyłania SMS.</p>
			',
		)));
		
		$languageData->AddChild(new FormEngine\Elements\Textarea(Array(
			'name' => 'smscomment',
			'label' => $this->trans('TXT_DEFAULT_ORDER_SMS_COMMENT')
		)));
		
		$requiredData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'orderstatusgroupsid',
			'label' => $this->trans('TXT_ORDER_STATUS_GROUPS'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_ORDER_STATUS_GROUPS'))
			),
			'options' => FormEngine\Option::Make(App::getModel('orderstatusgroups/orderstatusgroups')->getOrderStatusGroupsAllToSelect(), $this->registry->core->getDefaultValueToSelect())
		)));
		
		$Data = Event::dispatch($this, 'admin.orderstatus.initForm', Array(
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