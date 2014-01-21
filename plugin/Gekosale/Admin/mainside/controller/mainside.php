<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale. Zabronione jest usuwanie informacji o
 * licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 583 $
 * $Author: gekosale $
 * $Date: 2011-10-28 22:19:07 +0200 (Pt, 28 paź 2011) $
 * $Id: mainside.php 583 2011-10-28 20:19:07Z gekosale $
 */
namespace Gekosale;

use FormEngine;

class MainsideController extends Component\Controller\Admin
{

	public function index ()
	{
// 		$remaining = (int) App::getContainer()->get('session')->getActiveAccountDaysRemaining();
// 		if ($remaining < 15){
// 			if ($remaining > 0){
// 				$message = "Za {$remaining} dni upłynie ważność Twojego abonamentu. Dokonaj płatności aby zagwarantować nieprzerwane działanie sklepu.";
// 			}
// 			else{
// 				$message = "Upłynął okres ważności abonamentu. Aby dalej korzystać z WellCommerce, musisz dokonać jego przedłużenia.";
// 			}
// 			App::getContainer()->get('session')->setVolatileMessage($message);
// 			$this->registry->template->assign('message', $message);
			
			
			
// 			if(App::getContainer()->get('session')->getActivePaymentRedirected() == NULL){
// 				App::getContainer()->get('session')->setActivePaymentRedirected(1);
// 				App::redirect(__ADMINPANE__ . '/instancemanager');
// 			}
// 		}
		
		App::getModel('contextmenu')->setTitle($this->trans('TXT_FAST_TOOLS'));
		App::getModel('contextmenu')->add($this->trans('TXT_ADD_PRODUCT'), $this->getRouter()->url('admin', 'product', 'add'));
		App::getModel('contextmenu')->add($this->trans('TXT_ADD_ORDER'), $this->getRouter()->url('admin', 'order', 'add'));
		
		App::getModel('contextmenu')->add($this->trans('TXT_CHECK_ORDERS'), $this->getRouter()->url('admin', 'order'));
		App::getModel('contextmenu')->add($this->trans('TXT_SHOW_CLIENTS'), $this->getRouter()->url('admin', 'client'));
		
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->assign('from', date('Y/m/1'));
		$this->registry->template->assign('to', date('Y/m/d'));
		$this->registry->template->assign('summaryStats', $this->model->getSummaryStats());
		$this->registry->template->assign('topten', $this->model->getTopTen());
		$this->registry->template->assign('opinions', $this->model->getOpinions());
		$this->registry->template->assign('mostsearch', $this->model->getMostSearch());
		$this->registry->template->assign('lastorder', $this->model->getLastOrder());
		$this->registry->template->assign('newclient', $this->model->getNewClient());
		$this->registry->template->assign('clientOnline', $this->model->getClientOnline());
		$this->renderLayout();
	}

	public function view ()
	{
		$period = base64_decode($this->registry->core->getParam(1));
		if (strlen($period) > 0){
			$range = base64_decode($this->registry->core->getParam(1));
			if (strpos($range, '-') > 0){
				$dates = explode('-', $range);
				$request = Array(
					'from' => trim(str_replace('/', '-', $dates[0])),
					'to' => trim(str_replace('/', '-', $dates[1]))
				);
			}
			else{
				$request = Array(
					'from' => trim(str_replace('/', '-', $range)),
					'to' => trim(str_replace('/', '-', $range))
				);
			}
		}
		else{
			$request = Array(
				'from' => date('Y-m-1'),
				'to' => date('Y-m-d')
			);
		}
		switch ($this->registry->core->getParam(0)) {
			case 'sales':
				echo $this->model->salesChart($request);
				break;
			case 'orders':
				echo $this->model->ordersChart($request);
				break;
			case 'clients':
				echo $this->model->clientsChart($request);
				break;
			case 'products':
				echo $this->model->productsChart($request);
				break;
		}
	}

	public function confirm ()
	{
		$param = base64_decode($this->registry->core->getParam());
		$Data = App::getModel('mainside')->search($param);
		$html = '<div class="livesearch-results">';
		if (isset($Data['orders'])){
			$html .= '<h3>Zamówienia:</h3>';
			$html .= '<ul>';
			foreach ($Data['orders'] as $key => $result){
				$html .= $result;
			}
			$html .= '</ul>';
		}
		if (isset($Data['clients'])){
			$html .= '<h3>Klienci:</h3>';
			$html .= '<ul>';
			foreach ($Data['clients'] as $key => $result){
				$html .= $result;
			}
			$html .= '</ul>';
		}
		if (isset($Data['products'])){
			$html .= '<h3>Produkty:</h3>';
			$html .= '<ul>';
			foreach ($Data['products'] as $key => $result){
				$html .= $result;
			}
			$html .= '</ul>';
		}
		$html .= '</div>';
		echo $html;
	}
}