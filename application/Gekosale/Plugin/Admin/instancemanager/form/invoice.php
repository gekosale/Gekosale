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

class InvoiceForm extends Component\Form
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
		
		$invoiceData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'invoice_data',
			'label' => 'Lista faktur'
		)));
		
		$htmlProHeader = '
			<p><h3>Lista faktur Proforma</h3></p>
			<div class="invoice-list">
			<table cellpadding="0" cellspacing="0">
				<thead>
	   				<tr>
	     				<th>Numer</th>
	      				<th>Data wystawienia</th>
	      				<th>Termin płatności</th>
	      				<th>Wartość brutto</th>
	      				<th>Rodzaj dokumentu</th>
	      				<th>Status</th>
	      				<th>Opcje</th>
	   				</tr>
				</thead>
			<tbody>';
		
		$htmlVatHeader = '
			<p><h3>Lista faktur VAT</h3></p>
			<div class="invoice-list">
			<table cellpadding="0" cellspacing="0">
				<thead>
	   				<tr>
	     				<th>Numer</th>
	      				<th>Data wystawienia</th>
	      				<th>Termin płatności</th>
	      				<th>Wartość brutto</th>
	      				<th>Rodzaj dokumentu</th>
	      				<th>Status</th>
	      				<th>Opcje</th>
	   				</tr>
				</thead>
			<tbody>';
		
		$htmlPro = '';
		$htmlVat = '';
		
		foreach ($this->populateData['invoice_data'] as $invoice){
			
			$due = $invoice['razem_brutto'] - $invoice['zaplacono'];
			$downloadUrl = App::getURLAdressWithAdminPane() . 'instancemanager/confirm/invoice,' . $invoice['id'];
			$payurl = App::getURLAdressWithAdminPane() . 'instancemanager/view/payment,' . $invoice['id'];
			
			$status = ($due > 0) ? '<strong style="color:#990000;">Niezapłacona</strong>' : '<strong style="color:green;">Zapłacona</strong>';
			
			$kwota = number_format($invoice['razem_brutto'], 2, '.', '');
			
			if ($invoice['rodzaj_faktury'] == 'Faktura Proforma'){
				$htmlPro .= "
					<tr>
					<td><a href=\"{$downloadUrl}\">{$invoice['numer']}</a></td>
					<td>{$invoice['data_wystawienia']}</td>
					<td>{$invoice['termin_zaplaty']}</td>
					<td>{$kwota} PLN</td>
					<td>{$invoice['rodzaj_faktury']}</td>
					<td><span>{$status}</span></td>";
				if ($due > 0){
					$htmlPro .= "<td><a href=\"{$downloadUrl}\">Pobierz</a> | <a href=\"{$payurl}\">Opłać online</a></td>";
				}
				else{
					$htmlPro .= "<td><a href=\"{$downloadUrl}\">Pobierz</a></td>";
				}
				$htmlPro .= "</tr>";
			}
			
			if ($invoice['rodzaj_faktury'] == 'Faktura VAT'){
				
				$htmlVat .= "
					<tr>
					<td><a href=\"{$downloadUrl}\">{$invoice['numer']}</a></td>
					<td>{$invoice['data_wystawienia']}</td>
					<td>{$invoice['termin_zaplaty']}</td>
					<td>{$kwota} PLN</td>
					<td>{$invoice['rodzaj_faktury']}</td>
					<td><span>{$status}</span></td>";
				if ($due > 0){
					$htmlVat .= "<td><a href=\"{$downloadUrl}\">Pobierz</a> | <a href=\"{$payurl}\">Opłać online</a></td>";
				}
				else{
					$htmlVat .= "<td><a href=\"{$downloadUrl}\">Pobierz</a></td>";
				}
				$htmlVat .= "</tr>";
			}
		}
		
		$htmlPro = ($htmlPro == '') ? '<tr><td colspan="8">Nie ma jeszcze faktur Proforma. Faktury Proforma pojawią się w momencie zakupu usług płatnych WellCommerce i na ich podstawie będziesz mógł dokonać płatności.</td></tr>' : $htmlPro;
		$htmlVat = ($htmlVat == '') ? '<tr><td colspan="8">Na razie nie ma tutaj jeszcze faktur VAT. Pojawią się w momencie, gdy opłacisz pierwszą fakturę Proforma za usługi.</td></tr>' : $htmlVat;
		$htmlProFooter = '</tbody></table></div>';
		$htmlVatFooter = '</tbody></table></div>';
		
		$invoiceData->AddChild(new FormEngine\Elements\StaticText(Array(
			'text' => $htmlProHeader . $htmlPro . $htmlProFooter . $htmlVatHeader . $htmlVat . $htmlVatFooter
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