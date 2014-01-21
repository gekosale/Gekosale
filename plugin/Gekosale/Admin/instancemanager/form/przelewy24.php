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

class Przelewy24Form extends Component\Form
{
	protected $populateData;

	public function __construct ($registry)
	{
		parent::__construct($registry);
	}

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'przelewy24',
			'action' => '',
			'method' => 'post'
		));
		
		$infoData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'info_data',
			'label' => 'Informacje podstawowe'
		)));
		
		$infoData->AddChild(new FormEngine\Elements\StaticText(Array(
			'text' => '
				<p><img src="' . DESIGNPATH . '_images_panel/logos/przelewy24_logo" /></p>
				<p>WellCommerce umożliwia rozpoczęcie przyjmowania płatności elektronicznych bez podpisywania papierowej umowy i oczekiwania na aktywację konta. Dodatkowo dla naszych klientów oferowane są specjalnie niskie prowizje za przyjmowanie płatności. </p>
				<p>Wszyscy klienci WellCommerce posiadają obniżone prowizje od transakcji:</p>
				<ul>
					<li>2.3% od transakcji standardowych</li>
					<li>0.87% od transakcji natychmiastowych</li>
				</ul>
				<p>Integracja jest w pełni automatyczna i zaraz po zatwierdzeniu danych będziesz mógł przyjmować płatności w swoim sklepie.</p>
				<p>Zobacz więcej na stronie <a href="http://www.przelewy24.pl/" target="_blank">Przelewy24.pl</a></p>
		'
		)));
		
		$paymentData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'payment_data',
			'label' => 'Ustawienia integracji'
		)));
		
		$paymentData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Wybierz statusy zamówienia dla płatności zakończonej i anulowanej. W każdej chwili możesz je zmienić w konfiguracji modułu Przelewy24.pl</p>'
		)));
		
		$paymentData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'positiveorderstatusid',
			'label' => 'Status zamówienia dla płatności zakończonej',
			'options' => FormEngine\Option::Make(App::getModel('orderstatus')->getOrderStatusToSelect())
		)));
		
		$paymentData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'negativeorderstatusid',
			'label' => 'Status zamówienia dla płatności anulowanej',
			'options' => FormEngine\Option::Make(App::getModel('orderstatus')->getOrderStatusToSelect())
		)));
		
		if (! empty($this->populateData)){
			$form->Populate($this->populateData);
		}
		
		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		return $form;
	}
}