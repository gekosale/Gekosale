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
 * $Revision: 111 $
 * $Author: gekosale $
 * $Date: 2011-05-06 21:54:00 +0200 (Pt, 06 maj 2011) $
 * $Id: store.php 111 2011-05-06 19:54:00Z gekosale $
 */
namespace Gekosale;

use FormEngine;
use sfEvent;
use SoapClient;

class InstanceManagerController extends Component\Controller\Admin
{

	public function __construct ($registry)
	{
		parent::__construct($registry);
		$this->instance = new Instance();
		$this->module = $this->registry->core->getParam(0);
	}

	public function GetSummary ($request)
	{
		Arr::debug($request);
	}

	public function index ()
	{
		App::getRegistry()->xajaxInterface->registerFunction(array(
			'GetSummary',
			$this,
			'GetSummary'
		));
		
		/*
		 * Limits
		 */
		$limitsForm = App::getFormModel('instancemanager/limits')->initForm();
		$this->registry->template->assign('limitsForm', $limitsForm->Render());
		
		/*
		 * Client
		 */
		
		$client = $this->instance->getClient();
		$this->registry->template->assign('client', $client['result']['client']);
		
		/*
		 * Payment
		 */
		
		$populateData = Array(
			'payment_data' => Array(
				'period' => '1',
				'paymenttype' => '1'
			),
			'billing_data' => isset($client['result']['client']) ? $client['result']['client'] : Array(),
			'productprice1' => $client['result']['client']['productprice1'],
			'productprice6' => $client['result']['client']['productprice6'],
			'productprice12' => $client['result']['client']['productprice12']
		);
		
		App::getFormModel('instancemanager/pay')->setPopulateData($populateData);
		$payForm = App::getFormModel('instancemanager/pay')->initForm();
		
		if ($payForm->Validate(FormEngine\FE::SubmittedData())){
			$formData = $payForm->getSubmitValues();
			$formData['billing_data']['verified'] = 1;
			$this->instance->updateClientData($formData['billing_data']);
			switch ($formData['payment_data']['period']) {
				case 1:
					$price = $client['result']['client']['productprice1'];
					break;
				case 6:
					$price = $client['result']['client']['productprice6'];
					break;
				case 12:
					$price = $client['result']['client']['productprice12'];
					break;
			}
			$formData['period'] = $formData['payment_data']['period'];
			$formData['price'] = $price;
			$formData['productname'] = $client['result']['client']['productname'];
			$formData['domainname'] = $client['result']['client']['domainname'];
			$invoice = $this->instance->addInvoice($formData);
			
			if (! empty($invoice)){
				if ($formData['payment_data']['paymenttype'] == 2){
					App::getContainer()->get('session')->setVolatileMessage('Została wystawiona proforma ' . $invoice['result']['data']['Invoice']['fullnumber'] . '. Możesz ją pobrać wraz z drukiem przelewu bankowego.');
					App::redirect(__ADMINPANE__ . '/instancemanager/view/invoice,' . $invoice['result']['object_id']);
				}
				else{
					App::redirect(__ADMINPANE__ . '/instancemanager/view/payment,' . $invoice['result']['object_id']);
				}
			}
			else{
				App::getContainer()->get('session')->setVolatileMessage('Nie udało się automatycznie wygenerować faktury. Skontaktuj się z obsługą WellCommerce.');
				App::redirect(__ADMINPANE__ . '/instancemanager');
			}
		}
		
		$this->registry->template->assign('payForm', $payForm->Render());
		
		/*
		 * Invoices
		 */
		
		$invoices = $this->instance->getInvoicesForClient();
		$this->registry->template->assign('invoices', $invoices['result']['invoices']);
		
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->xajax->processRequest();
		
		$this->registry->template->display($this->loadTemplate('index.tpl'));
	}

	public function add ()
	{
	}

	public function view ()
	{
		switch ($this->module) {
			
			case 'account':
				
				$this->formModel = App::getFormModel('instancemanager/account');
				
				$client = $this->instance->getClient();
				
				$populateData = Array(
					'billing_data' => isset($client['result']['client']) ? $client['result']['client'] : Array()
				);
				
				$this->formModel->setPopulateData($populateData);
				
				$form = $this->formModel->initForm();
				
				if ($form->Validate(FormEngine\FE::SubmittedData())){
					try{
						$formData = $form->getSubmitValues();
						$this->instance->updateClientData($formData['billing_data']);
						App::getContainer()->get('session')->setVolatileMessage("Zaktualizowano dane abonenta.");
					}
					catch (Exception $e){
						$this->registry->template->assign('error', $e->getMessage());
					}
					if (FormEngine\FE::IsAction('continue')){
						App::getContainer()->get('session')->setActiveAccountValidationRequired(0);
						App::redirect(__ADMINPANE__ . '/instancemanager/view/account');
					}
					else{
						App::redirect(__ADMINPANE__ . '/instancemanager');
					}
				}
				
				$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
				$this->registry->xajax->processRequest();
				$this->registry->template->assign('form', $form->Render());
				$this->registry->template->display($this->loadTemplate('account.tpl'));
				
				break;
			case 'invoice':
				$invoice = $this->instance->getInvoice($this->registry->core->getParam(1));
				
				if (isset($invoice['result']) && ! empty($invoice['result'])){
					$data = base64_decode($invoice['result']['content']);
					header('Content-Type: application/pdf');
					header('Content-Description: File Transfer');
					header('Content-Transfer-Encoding: binary');
					header('Content-Disposition: attachment; filename="' . $invoice['result']['fullnumber'] . '.pdf"');
					header('Content-Length: ' . strlen($data));
					header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0');
					header('Expires: 0');
					echo $data;
					exit();
				}
				else{
					App::getContainer()->get('session')->setVolatileMessage('Nie można pobrać wskazanej faktury. Skontaktuj się z obsługą WellCommerce.');
					App::redirect(__ADMINPANE__ . '/instancemanager');
				}
				break;
			
			case 'payment':
				
				$invoice = $this->instance->getInvoice($this->registry->core->getParam(1));
				
				if (isset($invoice['result']) && ! empty($invoice['result'])){
					
					$paymentSettings = $this->instance->getPaymentSettings();
					
					/*
					 * Dev
					 */
					
// 					$paymentSettings['result']['gateway'] = 'https://sandbox.przelewy24.pl/index.php';
// 					$paymentSettings['result']['reporturl'] = 'https://sandbox.przelewy24.pl/transakcja.php';
					
					$client = $this->instance->getClient();
					
					$kwota = $invoice['result']['remaining'];
					
					$code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 12));
					
					$paymentData = Array(
						'p24_session_id' => $code . ':' . $this->registry->core->getParam(1),
						'p24_id_sprzedawcy' => $paymentSettings['result']['idsprzedawcy'],
						'p24_kwota' => number_format($kwota * 100, 0, '', ''),
						'p24_klient' => ($client['result']['client']['companyname'] != '') ? $client['result']['client']['companyname'] : $client['result']['client']['firstname'] . ' ' . $client['result']['client']['surname'],
						'p24_adres' => $client['result']['client']['street'] . ' ' . $client['result']['client']['streetno'] . (($client['result']['client']['placeno'] != '') ? '/' . $client['result']['client']['placeno'] : ''),
						'p24_kod' => $client['result']['client']['postcode'],
						'p24_miasto' => $client['result']['client']['city'],
						'p24_kraj' => 'PL',
						'p24_email' => $client['result']['client']['email'],
						'p24_return_url_ok' => App::getURLAdress() . 'instancereport',
						'p24_return_url_error' => App::getURLAdress() . 'instancereport',
						'p24_opis' => 'Faktura: ' . $invoice['result']['fullnumber'],
						'p24_crc' => md5($code . ':' . $this->registry->core->getParam(1) . '|' . $paymentSettings['result']['idsprzedawcy'] . '|' . (number_format($kwota * 100, 0, '', '')) . '|' . $paymentSettings['result']['crc'])
					);
					
					$this->registry->template->assign('paymentData', $paymentData);
					$this->registry->template->display($this->loadTemplate('payment.tpl'));
				}
				else{
					App::getContainer()->get('session')->setVolatileMessage('Nie można pobrać wskazanej faktury. Skontaktuj się z obsługą WellCommerce.');
					App::redirect(__ADMINPANE__ . '/instancemanager');
				}
				break;
			
			case 'przelewy24':
				
				$client = $this->instance->getClient();
				
				if (! isset($client['result']['client']['verified']) || $client['result']['client']['verified'] == 0){
					$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
					$this->registry->xajax->processRequest();
					$this->registry->template->assign('verified', 0);
					$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
					$this->registry->xajax->processRequest();
				}
				else{
					$this->formModel = App::getFormModel('instancemanager/przelewy24');
					$this->model = App::getModel('instancemanager/przelewy24api');
					$checkNip = $this->model->checkClientNip();
					$settings = $this->registry->core->loadModuleSettings('przelewy24', Helper::getViewId());
					
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
				<p><img src="' . DESIGNPATH . '_images_panel/logos/przelewy24_logo.png" /></p>
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
						'name' => 'przelewy24_data',
						'label' => 'Ustawienia integracji'
					)));
					
					if (! $this->model->testAccess()){
						$paymentData->AddChild(new FormEngine\Elements\Tip(Array(
							'tip' => '<p>Nie udało się połączyć z serwerem Przelewy24. Integracja automatyczna nie jest możliwa w tym momencie.</p>'
						)));
					}
					
					if (isset($checkNip['error'])){
						$paymentData->AddChild(new FormEngine\Elements\Tip(Array(
							'tip' => '<p>' . $checkNip['errorMsg'] . '</p>'
						)));
					}
					
					if (! empty($settings)){
						
						if ($settings['activelink'] != ''){
							$paymentData->AddChild(new FormEngine\Elements\Tip(Array(
								'tip' => "<p>Twoje konto jest już zarejestrowane, ale nie jest aktywne. Przejdź na stronę <a href=\"{$settings['activelink']}\" target=\"_blank\">{$settings['activelink']}</a> aby je aktywować.</p>"
							)));
						}
						
						$paymentData->AddChild(new FormEngine\Elements\Constant(Array(
							'name' => 'idsprzedawcy',
							'label' => 'Id sprzedawcy'
						)));
						
						$paymentData->AddChild(new FormEngine\Elements\Constant(Array(
							'name' => 'crc',
							'label' => 'Klucz do CRC'
						)));
					}
					
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
					
					if (! empty($settings)){
						$populate = Array(
							'przelewy24_data' => Array(
								'idsprzedawcy' => $settings['idsprzedawcy'],
								'crc' => $settings['crc'],
								'positiveorderstatusid' => $settings['positiveorderstatusid'],
								'negativeorderstatusid' => $settings['negativeorderstatusid']
							)
						);
						
						$form->Populate($populate);
					}
					
					if ($form->Validate(FormEngine\FE::SubmittedData())){
						try{
							$formData = $form->getSubmitValues();
							
							$p24client = $this->model->registerCompany();
							
							$active = 1;
							
							if (isset($p24client->error)){
								App::getContainer()->get('session')->setVolatileMessage($p24client->error->errorMessage);
								$active = 0;
							}
							
							if (isset($p24client->result->spid)){
								$Settings = Array(
									'idsprzedawcy' => $p24client->result->spid,
									'crc' => $p24client->result->crc,
									'positiveorderstatusid' => $formData['przelewy24_data']['positiveorderstatusid'],
									'negativeorderstatusid' => $formData['przelewy24_data']['negativeorderstatusid'],
									'activelink' => ($active == 1) ? '' : $p24client->result->link
								);
								$this->registry->core->saveModuleSettings('przelewy24', $Settings, Helper::getViewId());
								$this->model->enableModule();
							}
						}
						catch (Exception $e){
							$this->registry->template->assign('error', $e->getMessage());
						}
						
						App::redirect(__ADMINPANE__ . '/instancemanager/view/' . $this->module);
					}
					
					$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
					$this->registry->xajax->processRequest();
					$this->registry->template->assign('form', $form->Render());
				}
				
				$this->registry->template->display($this->loadTemplate('przelewy24.tpl'));
				
				break;
			case 'kurjerzy':
				
				$client = $this->instance->getClient();
				
				// --force=yes
				// $client['result']['client']['verified'] = 1;
				
				if (! isset($client['result']['client']['verified']) || $client['result']['client']['verified'] == 0){
					$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
					$this->registry->xajax->processRequest();
					$this->registry->template->assign('verified', 0);
					$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
					$this->registry->xajax->processRequest();
				}
				else{
					
					$this->formModel = App::getFormModel('instancemanager/kurjerzy');
					$this->model = App::getModel('instancemanager/kurjerzyapi');
					$settings = $this->registry->core->loadModuleSettings('kurjerzy', Helper::getViewId());
					
					$form = new FormEngine\Elements\Form(Array(
						'name' => 'kurjerzy',
						'action' => '',
						'method' => 'post'
					));
					
					$infoData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
						'name' => 'info_data',
						'label' => 'Informacje podstawowe'
					)));
					
					$priceList = $this->model->testAccess();
					
					$content = '<ul>';
					
					foreach ($priceList as $price){
						$content .= "<li style=\"margin:5px;\">{$price['productName']} - <strong>{$price['productPrice']} PLN</strong></li>";
					}
					
					$content .= '</ul>';
					
					$infoData->AddChild(new FormEngine\Elements\StaticText(Array(
						'text' => '<p><img src="' . DESIGNPATH . '_images_panel/logos/kurjerzy_logo.png" /></p><p>Serwis KurJerzy.pl został uruchomiony na początku 2010 roku jako alternatywa dla kosztownych usług kurierskich w Polsce. Firma działa jako pośrednik między Klientami a firmami kurierskimi UPS, DHL oraz FEDEX, a jej oferta skierowana jest zarówno do osób prywatnych, jak i przedsiębiorstw. Serwis daje możliwość bardzo niskim kosztem skorzystać z usług największych firm kurierskich w Polsce. Jego główną zaletą jest możliwość zamówienia usługi – do tej pory dostępnej tylko dla przedsiębiorstw – w sposób łatwy i szybki, bez konieczności podpisywania jakiejkolwiek umowy. Proponowane ceny są tak atrakcyjne, ponieważ dzięki dużej liczbie obsługiwanych przesyłek, firma osiągnęła znaczne zniżki u firm kurierskich.<h3>Cennik paczek:</h3>' . $content
					)));
					
					$paymentData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
						'name' => 'kurjerzy_data',
						'label' => 'Ustawienia integracji'
					)));
					
					if (isset($priceList['error'])){
						$paymentData->AddChild(new FormEngine\Elements\Tip(Array(
							'tip' => '<p>Nie udało się połączyć z serwerem KurJerzy. Integracja automatyczna nie jest możliwa w tym momencie.</p>'
						)));
					}
					
					if (! empty($settings)){
						
						$paymentData->AddChild(new FormEngine\Elements\Constant(Array(
							'name' => 'apikey',
							'label' => 'Klucz (apiKey)'
						)));
						
						$paymentData->AddChild(new FormEngine\Elements\Constant(Array(
							'name' => 'apipin',
							'label' => 'Pin (apiPin)'
						)));
						$paymentData->AddChild(new FormEngine\Elements\Constant(Array(
							'name' => 'login',
							'label' => $this->trans('TXT_LOG')
						)));
						
						$paymentData->AddChild(new FormEngine\Elements\Constant(Array(
							'name' => 'password',
							'label' => $this->trans('TXT_PASSWORD')
						)));
					}
					else{
						$paymentData->AddChild(new FormEngine\Elements\StaticText(Array(
							'text' => '<p>Zatwierdź formularz aby automatycznie założyć konto w serwisie KurJerzy.pl. Rejestracja jest w pełni bezpłatna, a po niej otrzymasz dane dostępowe do konta w serwisie.</p>'
						)));
					}
					
					if (! empty($settings)){
						$populate = Array(
							'kurjerzy_data' => Array(
								'apikey' => $settings['apikey'],
								'apipin' => $settings['apipin'],
								'login' => $settings['login'],
								'password' => $settings['password']
							)
						);
						
						$this->registry->template->assign('disableNavigation', 1);
						$form->Populate($populate);
					}
					
					if ($form->Validate(FormEngine\FE::SubmittedData())){
						try{
							$formData = $form->getSubmitValues();
							
							$kurierzyClient = $this->model->registerCompany();
							
							$active = 1;
							
							if (isset($kurierzyClient['error'])){
								App::getContainer()->get('session')->setVolatileMessage($kurierzyClient['error']['message']);
								$active = 0;
							}
							else{
								$Settings = Array(
									'apikey' => $kurierzyClient['userApiKey'],
									'apipin' => $kurierzyClient['userApiPin'],
									'login' => $kurierzyClient['login'],
									'password' => $kurierzyClient['passwd']
								);
								$this->registry->core->saveModuleSettings('kurjerzy', $Settings, Helper::getViewId());
								App::getContainer()->get('session')->setVolatileMessage('Integracja KurJerzy.pl przebiegła pomyślnie');
							}
						}
						catch (Exception $e){
							$this->registry->template->assign('error', $e->getMessage());
						}
						
						App::redirect(__ADMINPANE__ . '/instancemanager/view/' . $this->module);
					}
					
					$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
					$this->registry->xajax->processRequest();
					$this->registry->template->assign('form', $form->Render());
				}
				
				$this->registry->template->display($this->loadTemplate('kurjerzy.tpl'));
				
				break;
			case 'sendingo':
				$client = $this->instance->getClient();
				
				if (! isset($client['result']['client']['verified']) || $client['result']['client']['verified'] == 0){
					$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
					$this->registry->xajax->processRequest();
					$this->registry->template->assign('verified', 0);
					$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
					$this->registry->xajax->processRequest();
				}
				else{
					$this->registry->xajaxInterface->registerFunction(array(
						'AddSendingoGroup',
						App::getModel('sendingo'),
						'addGroup'
					));
					
					$this->formModel = App::getFormModel('instancemanager/sendingo');
					$this->model = App::getModel('instancemanager/sendingoapi');
					$settings = $this->registry->core->loadModuleSettings('sendingo');
					
					$form = new FormEngine\Elements\Form(Array(
						'name' => 'sendingo',
						'action' => '',
						'method' => 'post'
					));
					
					$infoData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
						'name' => 'info_data',
						'label' => 'Informacje podstawowe'
					)));
					
					$infoData->AddChild(new FormEngine\Elements\StaticText(Array(
						'text' => '<p><img src="' . DESIGNPATH . '_images_panel/logos/sendingo_logo.png" /></p><p>Platforma email marketingowa zaprojektowana i konsekwentnie rozwijana z myślą o eCommerce i LifeCycle Email Marketingu, która zyskała uznanie za intuicyjny interfejs i najwyższej jakości obsługę.</p>'
					)));
					
					$sendingoData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
						'name' => 'sendingo_data',
						'label' => 'Ustawienia integracji'
					)));
					
					if (! empty($settings['auth_token'])){
						$sendingoData->AddChild(new FormEngine\Elements\TextField(Array(
							'name' => 'username',
							'label' => 'Nazwa użytkownika'
						)));
						$sendingoData->AddChild(new FormEngine\Elements\TextField(Array(
							'name' => 'password',
							'label' => $this->trans('TXT_PASSWORD')
						)));
						$sendingoData->AddChild(new FormEngine\Elements\Password(Array(
							'name' => 'auth_token',
							'label' => 'Token autoryzacyjny'
						)));
						
						$sendingoData->AddChild(new FormEngine\Elements\Tip(Array(
							'tip' => '<p>Wybierz listy, do których ma zostać zapisany subskrybent.</p>',
							'direction' => FormEngine\Elements\Tip::DOWN
						)));
						
						$sendingoData->AddChild(new FormEngine\Elements\MultiSelect(Array(
							'name' => 'groups',
							'label' => $this->trans('TXT_CLIENTGROUPS'),
							'addable' => true,
							'onAdd' => 'xajax_AddSendingoGroup',
							'add_item_prompt' => 'Podaj nazwę grupy',
							'options' => FormEngine\Option::Make((array) @unserialize($settings['groups']))
						)));
					}
					else{
						$sendingoData->AddChild(new FormEngine\Elements\StaticText(Array(
							'text' => '<p>Zatwierdź formularz aby automatycznie założyć konto w serwisie Sendingo. Rejestracja jest w pełni bezpłatna, a po niej otrzymasz dane dostępowe do konta w serwisie.</p>'
						)));
						
						$customConfiguration = $sendingoData->addChild(new FormEngine\Elements\Checkbox(array(
							'name' => 'custom_configuration',
							'label' => 'Ręczna konfiguracja'
						)));
						
						$sendingoData->AddChild(new FormEngine\Elements\TextField(Array(
							'name' => 'username',
							'label' => 'Nazwa użytkownika',
							'dependencies' => array(
								new FormEngine\Dependency(FormEngine\Dependency::SHOW, $customConfiguration, new FormEngine\Conditions\Equals(1))
							)
						)));
						
						$sendingoData->AddChild(new FormEngine\Elements\TextField(Array(
							'name' => 'password',
							'label' => 'Hasło',
							'dependencies' => array(
								new FormEngine\Dependency(FormEngine\Dependency::SHOW, $customConfiguration, new FormEngine\Conditions\Equals(1))
							)
						)));
						$sendingoData->AddChild(new FormEngine\Elements\TextField(Array(
							'name' => 'auth_token',
							'label' => 'Token autoryzacyjny',
							'dependencies' => array(
								new FormEngine\Dependency(FormEngine\Dependency::SHOW, $customConfiguration, new FormEngine\Conditions\Equals(1))
							)
						)));
					}
					
					if (! empty($settings['auth_token'])){
						$populate = Array(
							'sendingo_data' => Array(
								'auth_token' => $settings['auth_token'],
								'username' => $settings['username'],
								'password' => $settings['password']
							)
						);
						
						$form->Populate($populate);
					}
					
					if ($form->Validate(FormEngine\FE::SubmittedData())){
						try{
							$formData = $form->getSubmitValues();
							
							if (isset($formData['sendingo_data']['custom_configuration']) && $formData['sendingo_data']['custom_configuration'] == 1){
								$Settings = array(
									'auth_token' => $formData['sendingo_data']['auth_token'],
									'username' => $formData['sendingo_data']['username'],
									'password' => $formData['sendingo_data']['password']
								);
								$this->registry->core->saveModuleSettings('sendingo', $Settings);
								
								$sendingoClient = App::getModel('sendingo');
								$sendingoClient->loadConfig();
								if ($sendingoClient->getSendingoGroups() === array()){
									$this->registry->core->saveModuleSettings('sendingo', array());
									App::getContainer()->get('session')->setVolatileMessage("Wystąpił problem z API Sendingo. Sprawdź poprawność danych");
								}
								else{
									App::getContainer()->get('session')->setVolatileMessage('Integracja z Sendingo przebiegła pomyślnie');
								}
							}
							else{
								if (empty($settings['auth_token'])){
									$sendingoClient = $this->model->registerCompany();
									
									$active = 1;
									
									if ($sendingoClient === FALSE){
										App::getContainer()->get('session')->setVolatileMessage("Wystąpił problem z API Sendingo.");
										$active = 0;
									}
									else{
										$Settings = Array(
											'auth_token' => $sendingoClient['auth_token'],
											'username' => $sendingoClient['username'],
											'password' => $sendingoClient['password'],
											'groups' => ''
										);
										$this->registry->core->saveModuleSettings('sendingo', $Settings);
										App::getContainer()->get('session')->setVolatileMessage('Integracja z Sendingo przebiegła pomyślnie');
									}
								}
								else{
									$Settings = array(
										'auth_token' => $formData['sendingo_data']['auth_token'],
										'username' => $formData['sendingo_data']['username'],
										'password' => $formData['sendingo_data']['password'],
										'groups' => $formData['sendingo_data']['groups']
									);
									$this->registry->core->saveModuleSettings('sendingo', $Settings);
									
									$sendingoClient = App::getModel('sendingo');
									$sendingoClient->loadConfig();
									if ($sendingoClient->getSendingoGroups() === array()){
										$this->registry->core->saveModuleSettings('sendingo', array());
										App::getContainer()->get('session')->setVolatileMessage("Wystąpił problem z API Sendingo. Sprawdź poprawność danych");
									}
									else{
										App::getContainer()->get('session')->setVolatileMessage('Integracja z Sendingo przebiegła pomyślnie');
									}
								}
							}
						}
						catch (Exception $e){
							$this->registry->template->assign('error', $e->getMessage());
						}
						
						App::redirect(__ADMINPANE__ . '/instancemanager/view/' . $this->module);
					}
					
					$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
					$this->registry->xajax->processRequest();
					$this->registry->template->assign('form', $form->Render());
				}
				
				$this->registry->template->display($this->loadTemplate('sendingo.tpl'));
				
				break;
			case 'furgonetka':
				$client = $this->instance->getClient();
				if (! isset($client['result']['client']['verified']) || $client['result']['client']['verified'] == 0){
					$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
					$this->registry->xajax->processRequest();
					$this->registry->template->assign('verified', 0);
					$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
					$this->registry->xajax->processRequest();
				}
				else{
					$this->model = App::getModel('instancemanager/furgonetkaapi');
					$settings = $this->registry->core->loadModuleSettings('furgonetka');
					$form = new FormEngine\Elements\Form(Array(
						'name' => 'furgonetka',
						'action' => '',
						'method' => 'post'
					));
					$infoData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
						'name' => 'info_data',
						'label' => 'Informacje podstawowe'
					)));
					
					$infoData->AddChild(new FormEngine\Elements\StaticText(Array(
					'text' => '<p><img src="' . DESIGNPATH . '_images_panel/logos/furgonetka_logo.png" /></p>
					<p>Serwis Furgonetka.pl powstał w grudniu 2010 roku. Naszą misją jest wsparcie rynku e-commerce w Polsce, dlatego oferujemy Państwu wygodne narzędzie, które pozwala zarówno firmom, jak i osobom prywatnym szybko i tanio wysyłać przesyłki kurierskie. Naszym klientom oferujemy najwyższej jakości usługi w bardzo atrakcyjnych cenach oraz szeroki wachlarz opcji dodatkowych.</p>
					<p>Serwis Furgonetka.pl należy do grupy serwisów firmy Swistak.pl sp. z o.o., która jest właścicielem m.in. platformy aukcyjnej www.swistak.pl.</p>
					'
					)));
					
					$furgonetkaData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
						'name' => 'furgonetka_data',
						'label' => 'Ustawienia integracji'
					)));
					if (! empty($settings)){
						$furgonetkaData->AddChild(new FormEngine\Elements\Constant(Array(
							'name' => 'login',
							'label' => 'Adres email'
						)));
						$furgonetkaData->AddChild(new FormEngine\Elements\Constant(Array(
							'name' => 'password',
							'label' => 'Hasło'
						)));
					}
					else{
						list($token, $img) = $this->model->getCaptcha();
						$furgonetkaData->AddChild(new FormEngine\Elements\StaticText(Array(
							'text' => '<p style="margin-left: 200px;">' . $img . '</p>'
						)));
						$furgonetkaData->addChild(new FormEngine\Elements\TextField(array(
							'name' => 'code',
							'label' => 'Kod z obrazka'
						)));
						$furgonetkaData->addChild(new FormEngine\Elements\Hidden(array(
							'name' => 'token',
							'default' => $token
						)));
					}
					if (! empty($settings)){
						$populate = Array(
							'furgonetka_data' => Array(
								'login' => $settings['login'],
								'password' => $settings['password']
							)
						);
						$this->registry->template->assign('disableNavigation', 1);
						$form->Populate($populate);
					}
					if ($form->Validate(FormEngine\FE::SubmittedData())){
						try{
							$formData = $form->getSubmitValues();
							$furgonetkaData = $this->model->registerCompany($formData['furgonetka_data']['token'], $formData['furgonetka_data']['code']);
							$active = 1;
							if ($furgonetkaData === False){
								App::getContainer()->get('session')->setVolatileMessage("Wystąpił problem z API Furgonetka.");
								$active = 0;
							}
							else{
								$Settings = Array(
									'login' => $furgonetkaData['login'],
									'password' => $furgonetkaData['password']
								);
								$this->registry->core->saveModuleSettings('furgonetka', $Settings);
								App::getContainer()->get('session')->setVolatileMessage('Integracja z Furgonetka.pl przebiegła pomyślnie');
							}
						}
						catch (Exception $e){
							$this->registry->template->assign('error', $e->getMessage());
						}
						App::redirect(__ADMINPANE__ . '/instancemanager/view/' . $this->module);
					}
					$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
					$this->registry->xajax->processRequest();
					$this->registry->template->assign('form', $form->Render());
				}
				$this->registry->template->display($this->loadTemplate('furgonetka.tpl'));
				break;
			case 'transferuj':
				
				$client = $this->instance->getClient();
				
				if (! isset($client['result']['client']['verified']) || $client['result']['client']['verified'] == 0){
					$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
					$this->registry->xajax->processRequest();
					$this->registry->template->assign('verified', 0);
				}
				else{
					$settings = $this->registry->core->loadModuleSettings('transferuj', Helper::getViewId());
					
					if (! empty($settings)){
						$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
						$this->registry->xajax->processRequest();
						$this->registry->template->assign('transferujactive', 1);
					}
					else{
						
						$this->formModel = App::getFormModel('instancemanager/transferuj');
						
						$result = App::getModel('instancemanager/transferujapi')->getUniqueCode();
						
						if ((int) $result->result == 0){
							App::redirect(__ADMINPANE__ . '/instancemanager/view/' . $this->module);
						}
						
						$populateData = Array(
							'payment_data' => Array(
								'type' => 'corp',
								'addr_name' => $client['result']['client']['companyname'],
								'addr_street' => $client['result']['client']['street'],
								'addr_block' => $client['result']['client']['streetno'],
								'addr_nr' => $client['result']['client']['placeno'],
								'addr_city' => $client['result']['client']['city'],
								'addr_code' => $client['result']['client']['postcode'],
								'addr_country' => $client['result']['client']['countryname'],
								'addr_phone' => $client['result']['client']['phone'],
								'email' => $client['result']['client']['email'],
								'webpages' => $client['result']['client']['domainname'],
								'code' => (string) $result->code,
								'addr_pesel' => '',
								'addr_taxid' => $client['result']['client']['nip'],
								'addr_regon' => $client['result']['client']['regon'],
								'gen_conf_code' => 1
							)
						);
						
						$this->formModel->setPopulateData($populateData);
						
						$form = $this->formModel->initForm();
						
						if ($form->Validate(FormEngine\FE::SubmittedData())){
							try{
								$formData = $form->getSubmitValues();
								$fields = $formData['payment_data'];
								$fields_string = '';
								
								foreach ($fields as $key => $value){
									$fields_string .= $key . '=' . urlencode($value) . '&';
								}
								rtrim($fields_string, '&');
								
								$url = 'https://secure.transferuj.pl/api/faab7ead3846a2d5e99331f5d5e533b0/register/index';
								
								$ci = curl_init();
								curl_setopt($ci, CURLOPT_USERAGENT, 'WellCommerce API');
								curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
								curl_setopt($ci, CURLOPT_TIMEOUT, 30);
								curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
								curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
								curl_setopt($ci, CURLOPT_HEADER, FALSE);
								curl_setopt($ci, CURLOPT_POST, TRUE);
								curl_setopt($ci, CURLOPT_POSTFIELDS, $fields_string);
								curl_setopt($ci, CURLOPT_URL, $url);
								$response = curl_exec($ci);
								curl_close($ci);
								
								$xml = simplexml_load_string($response);
								
								if ((int) $xml->result == 1){
									$Settings = Array(
										'idsprzedawcy' => (string) $xml->seller_id,
										'kodsprzedawcy' => (string) $xml->confirmation_code,
										'positiveorderstatusid' => $formData['payment_data']['positiveorderstatusid'],
										'negativeorderstatusid' => $formData['payment_data']['negativeorderstatusid'],
										'apikey' => (string) $xml->key
									);
									$this->registry->core->saveModuleSettings('transferuj', $Settings, Helper::getViewId());
									App::getModel('instancemanager/transferujapi')->enableModule();
									App::getContainer()->get('session')->setVolatileMessage('Integracja przebiegła pomyślnie. Możesz korzystać z płatności Transferuj.pl');
								}
								else{
									App::getContainer()->get('session')->setVolatileMessage('Nie udało się założyć konta w Transferuj.pl. Skontaktuj się z obsługą techniczną.');
								}
								
								App::redirect(__ADMINPANE__ . '/instancemanager/view/' . $this->module);
							}
							catch (Exception $e){
								$this->registry->template->assign('error', $e->getMessage());
							}
						}
						$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
						$this->registry->xajax->processRequest();
						$this->registry->template->assign('form', $form->Render());
					}
				}
				
				$this->registry->template->display($this->loadTemplate('transferuj.tpl'));
				break;
		}
	}
}