<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms 
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the 
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace Gekosale;

class InstanceReportController extends Component\Controller\Frontend
{

	public function index ()
	{
		if (isset($_POST['p24_session_id']) && $_POST['p24_session_id'] != ''){
			
			$this->instance = new Instance();
			
			$invoiceId = end(explode(':', $_POST['p24_session_id']));
			
			$invoice = $this->instance->getInvoice($invoiceId);
			
			$kwota = number_format($invoice['result']['remaining'] * 100, 0, '', '');
			
			if (isset($invoice['result']) && ! empty($invoice['result'])){
				if ($kwota == $_POST['p24_kwota']){
					$paymentSettings = $this->instance->getPaymentSettings();
// 					$paymentSettings['result']['reporturl'] = 'https://sandbox.przelewy24.pl/transakcja.php';
					$P = array();
					$RET = array();
					$url = $paymentSettings['result']['reporturl'];
					$P[] = "p24_id_sprzedawcy=" . $paymentSettings['result']['idsprzedawcy'];
					$P[] = "p24_session_id=" . $_POST["p24_session_id"];
					$P[] = "p24_order_id=" . $_POST["p24_order_id"];
					$P[] = "p24_kwota=" . number_format($kwota, 0, '', '');
					$user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, join("&", $P));
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
					curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					$result = curl_exec($ch);
					curl_close($ch);
					$T = explode(chr(13) . chr(10), $result);
					$res = false;
					foreach ($T as $line){
						$line = ereg_replace("[\n\r]", "", $line);
						if ($line != "RESULT" and ! $res)
							continue;
						if ($res)
							$RET[] = $line;
						else
							$res = true;
					}
					if ($RET[0] == 'TRUE'){
						$result = $this->instance->confirmPayment($invoice['result']['id'], number_format($_POST['p24_kwota'] / 100, 2, '.', ''));
						if (App::getContainer()->get('session')->getActiveUserid() > 0){
							App::getContainer()->get('session')->setVolatileMessage("Dziękujemy za dokonanie płatności.");
							App::redirect(__ADMINPANE__ . '/instancemanager');
						}
						else{
							App::redirect('');
						}
					}
				}
			}
		}
	}

	public function disable ()
	{
		if ($this->registry->loader->isOffline() == 0){
			$this->instance = new Instance();
			$client = $this->instance->getClient();
			if (isset($client['result']['client']['deletable']) && $client['result']['client']['deletable'] == 1){
				$this->instance->disableInstance();
			}
		}
	}

	public function enable ()
	{
		if ($this->registry->loader->isOffline() == 1){
			$this->instance = new Instance();
			$client = $this->instance->getClient();
			if (isset($client['result']['client']['deletable']) && $client['result']['client']['deletable'] == 0){
				$this->instance->enableInstance();
			}
		}
	}

	public function limits ()
	{
		$this->instance = new Instance();
		Arr::debug($this->instance->getCurrentLimits());
	}
}
