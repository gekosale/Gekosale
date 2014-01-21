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

class PaymentForm extends Component\Form
{
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'invoice',
			'action' => 'https://secure.przelewy24.pl',
// 			'action' => 'https://sandbox.przelewy24.pl/index.php',
			'method' => 'post',
			'tabs' => 1
		));
		
		$paymentData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'payment_data',
			'label' => 'Płatności'
		)));
		
		$paymentData->AddChild(new FormEngine\Elements\StaticText(Array(
			'text' => "<p>Trwa przekierowanie na strony systemu Przelewy24.pl. Prosimy czekać...</p>"
		)));
		
		$form->AddChild(new FormEngine\Elements\Hidden(Array(
			'name' => 'p24_session_id'
		)));
		
		$form->AddChild(new FormEngine\Elements\Hidden(Array(
			'name' => 'p24_id_sprzedawcy'
		)));
		
		$form->AddChild(new FormEngine\Elements\Hidden(Array(
			'name' => 'p24_kwota'
		)));
		
		$form->AddChild(new FormEngine\Elements\Hidden(Array(
			'name' => 'p24_klient'
		)));
		
		$form->AddChild(new FormEngine\Elements\Hidden(Array(
			'name' => 'p24_adres'
		)));
		
		$form->AddChild(new FormEngine\Elements\Hidden(Array(
			'name' => 'p24_kod'
		)));
		
		$form->AddChild(new FormEngine\Elements\Hidden(Array(
			'name' => 'p24_miasto'
		)));
		
		$form->AddChild(new FormEngine\Elements\Hidden(Array(
			'name' => 'p24_kraj'
		)));
		
		$form->AddChild(new FormEngine\Elements\Hidden(Array(
			'name' => 'p24_email'
		)));
		
		$form->AddChild(new FormEngine\Elements\Hidden(Array(
			'name' => 'p24_return_url_ok'
		)));
		
		$form->AddChild(new FormEngine\Elements\Hidden(Array(
			'name' => 'p24_return_url_error'
		)));
		
		$form->AddChild(new FormEngine\Elements\Hidden(Array(
			'name' => 'p24_opis'
		)));
		
		$form->AddChild(new FormEngine\Elements\Hidden(Array(
			'name' => 'p24_crc'
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