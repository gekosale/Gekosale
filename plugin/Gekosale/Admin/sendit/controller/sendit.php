<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2011 Gekosale. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 *
 */

namespace Gekosale;

use Gekosale\App;
use Gekosale\Helper;

class SenditController extends Component\Controller\Admin
{
	public function index ()
	{
		$model = App::getModel('order');

		$this->registry->xajax->registerFunction(array(
			'doChangeOrderStatus',
			$model,
			'doAJAXChangeOrderStatus'
		));

		$this->registry->xajax->registerFunction(array(
			'doDeleteOrder',
			$model,
			'doAJAXDeleteOrder'
		));

		$this->registry->xajax->registerFunction(array(
			'LoadAllOrder',
			$model,
			'getOrderForAjax'
		));

		$this->registry->xajax->registerFunction(array(
			'GetClientSuggestions',
			$model,
			'getClientForAjax'
		));

		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('datagrid_filter', $model->getDatagridFilterData());
		$this->registry->template->assign('order_statuses', json_encode(App::getModel('orderstatus')->getOrderStatusToSelect()));
		$this->registry->template->display($this->loadTemplate('index.tpl'));
	}

	public function view()
	{
		$error = '';

		if (strtolower($this->registry->core->getParam(1)) == 'print')
		{
			switch (strtolower($this->registry->core->getParam(2)))
			{
				case 'lp':
					$error = $this->model->printLp($this->registry->core->getParam(3));
					break;
				case 'protocol':
					$error = $this->model->printProtocol($this->registry->core->getParam(3));
					break;
				default:
					echo 'blad';
					$error = 'Nieprawidłowy parametr';
			}
		}

		$settings = $this->registry->core->loadModuleSettings('sendit', Helper::getViewId());

		$order = App::getModel('order')->getOrderById((int) $this->registry->core->getParam());
		$weight = $this->model->getProductsWeight((int) $this->registry->core->getParam());
		$receiverCountryCode = $this->model->getCountryIso($order['delivery_address']['countryid']);
		$receiverName = ($order['delivery_address']['companyname'] != '' )?$order['delivery_address']['companyname'] : $order['delivery_address']['firstname'].' '.$order['delivery_address']['surname'];

		$data = array(
			'senderName' => $settings['SENDIT_NAME'],
			'senderStreet' => $settings['SENDIT_STREET'],
			'senderZip' => $settings['SENDIT_ZIP'],
			'senderCity' => $settings['SENDIT_CITY'],
			'senderPhone' => $settings['SENDIT_PHONE'],
			'senderEmail' => $settings['SENDIT_EMAIL'],
			'senderPerson' => $settings['SENDIT_PERSON'],

			'receiverCountryCode'	=> $receiverCountryCode,
			'receiverEmail'		=> $order['delivery_address']['email'],
			'receiverName'		=> $receiverName,
			'receiverStreet'	=> $order['delivery_address']['street'].' '.$order['delivery_address']['streetno'],
			'receiverCity'		=> $order['delivery_address']['city'],
			'receiverPhone'		=> $order['delivery_address']['phone'],
			'receiverZip'		=> $order['delivery_address']['postcode'],
			'receiverPerson'	=> $receiverName,

			'country_list' => $this->model->getCountryList(),

			'kPK'				=> 0,
			'kP5'				=> ($weight <= 5)?1:0,
			'kP10'				=> ($weight <= 10 && $weight > 5)?1:0,
			'kP20'				=> ($weight <= 20 && $weight > 10)?1:0,
			'kP30'				=> ($weight <= 30 && $weight > 20)?1:0,
			'kP50'				=> ($weight <= 50 && $weight > 30)?1:0,
			'kP70'				=> ($weight <= 70 && $weight > 50)?1:0,
			'sendit_error'		=> $error,

		);
		$this->registry->xajaxInterface->registerFunction(array(
			'checkService',
			$this->model,
			'checkService'
		));
		$this->registry->xajaxInterface->registerFunction(array(
			'rate',
			$this->model,
			'rate'
		));
		$this->registry->xajaxInterface->registerFunction(array(
			'confirmOrder',
			$this->model,
			'confirmOrder'
		));
		$this->registry->xajaxInterface->registerFunction(array(
			'updateStatus',
			$this->model,
			'updateStatus'
		));

		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->xajax->processRequest();
		foreach ($data as $k => $d) {
			$this->registry->template->assign($k, $d);
		}
		$this->registry->template->assign('order', $order);
		$this->registry->template->display($this->loadTemplate('view.tpl'));
	}
}