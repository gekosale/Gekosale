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
 * $Revision: 528 $
 * $Author: gekosale $
 * $Date: 2011-09-12 08:54:55 +0200 (Pn, 12 wrz 2011) $
 * $Id: platnosci.php 528 2011-09-12 06:54:55Z gekosale $
 */
namespace Gekosale;

use FormEngine;

class PaybynetModel extends Component\Model
{
	//const URL = 'https://pbn.paybynet.com.pl/PayByNet/trans.do';
	const URL = 'https://pbn.paybynet.com.pl/PayByNetT/trans.do';

	//const WSDL = 'https://pbn.paybynet.com.pl/axis/services/PBNTransactionsGetStatus?wsdl';
	const WSDL = 'https://pbn.paybynet.com.pl/axist/services/PBNTransactionsGetStatus?wsdl';

	protected $_name = 'PayByNet';

	public function getPaymentMethod ($event, $request)
	{
		$Data[$this->getName()] = $this->_name;
		$event->setReturnValues($Data);
	}

	public function getPaymentMethodConfigurationForm ($event, $request)
	{
		if ($request['data']['paymentmethodmodel'] != $this->getName()){
			return false;
		}

		$paybynet = &$request['form']->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'paybynet_data',
			'label' => $this->trans('TXT_CONFIGURATION')
		)));

		$paybynet->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'id_client',
			'label' => $this->trans('TXT_NIP'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('TXT_REQUIRED')),
				new FormEngine\Rules\Format($this->trans('ERR_WRONG_FORMAT'), '/^[0-9 -]+$/')
			)
		)));

		$paybynet->AddChild(new FormEngine\Elements\Password(Array(
			'name' => 'password',
			'label' => $this->trans('TXT_PASSWORD'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('TXT_REQUIRED'))
			)
		)));

		$accname = $paybynet->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'accname',
			'label' => 'Rachunek bankowy',
		)));

		$accname->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'accname_ac',
			'label' => 'Numer rachunku',
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('TXT_REQUIRED')),
				new FormEngine\Rules\Format($this->trans('ERR_WRONG_FORMAT'), '/^[0-9 -]+$/')
			)
		)));

		$accname->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'accname_nm',
			'label' => 'Nazwa  rachunku bankowego',
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('TXT_REQUIRED'))
			)
		)));

		$accname->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'accname_zp',
			'label' => $this->trans('TXT_POSTCODE'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('TXT_REQUIRED'))
			)
		)));

		$accname->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'accname_ci',
			'label' => $this->trans('TXT_PLACENAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('TXT_REQUIRED'))
			)
		)));

		$accname->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'accname_st',
			'label' => $this->trans('TXT_STREET'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('TXT_REQUIRED'))
			)
		)));

		$accname->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'accname_ct',
			'label' => $this->trans('TXT_COUNTRY'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('TXT_REQUIRED'))
			)
		)));

		$paybynet->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'verificationrejectedorderstatusid',
			'label' => 'Weryfikacja odrzucona',
			'options' => FormEngine\Option::Make(App::getModel('orderstatus')->getOrderStatusToSelect())
		)));

		$paybynet->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'positiveorderstatusid',
			'label' => 'Status zamówienia dla płatności zakończonej',
			'options' => FormEngine\Option::Make(App::getModel('orderstatus')->getOrderStatusToSelect())
		)));

		$paybynet->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'negativeorderstatusid',
			'label' => 'Status zamówienia dla płatności anulowanej',
			'options' => FormEngine\Option::Make(App::getModel('orderstatus')->getOrderStatusToSelect())
		)));


		$settings = $this->registry->core->loadModuleSettings('paybynet', Helper::getViewId());

		if (! empty($settings)){
			$populate = Array(
				'paybynet_data' => Array(
					'id_client' => $settings['id_client'],
					'accname' => array(
						'accname_ac' => $settings['accname_ac'],
						'accname_nm' => $settings['accname_nm'],
						'accname_zp' => $settings['accname_zp'],
						'accname_ci' => $settings['accname_ci'],
						'accname_st' => $settings['accname_st'],
						'accname_ct' => $settings['accname_ct']
					),
					'password' => $settings['password'],
					'positiveorderstatusid' => $settings['positiveorderstatusid'],
					'negativeorderstatusid' => $settings['negativeorderstatusid'],
					'verificationrejectedorderstatusid' => $settings['verificationrejectedorderstatusid']
				)
			);

			$event->setReturnValues($populate);
		}
	}

	public function saveSettings ($request)
	{
		if ($request['model'] != $this->getName()){
			return false;
		}

		$Settings = Array(
			'id_client' => preg_replace('~[^\d]~', '', $request['data']['id_client']),
			'accname_ac' => preg_replace('~[^\d]~', '', $request['data']['accname_ac']),
			'accname_nm' => $request['data']['accname_nm'],
			'accname_zp' => $request['data']['accname_zp'],
			'accname_ci' => $request['data']['accname_ci'],
			'accname_st' => $request['data']['accname_st'],
			'accname_ct' => $request['data']['accname_ct'],
			'password' => $request['data']['password'],
			'positiveorderstatusid' => $request['data']['positiveorderstatusid'],
			'negativeorderstatusid' => $request['data']['negativeorderstatusid'],
			'verificationrejectedorderstatusid' => $request['data']['verificationrejectedorderstatusid']
		);

		$this->registry->core->saveModuleSettings('paybynet', $Settings, Helper::getViewId());
	}

	public function getPaymentData ($order)
	{
		$settings = $this->registry->core->loadModuleSettings('paybynet', Helper::getViewId());

		$idTrans = str_pad($order['orderId'], 10, '0', STR_PAD_LEFT);
		$dateValid = date('d-m-Y H:i:s', time()+604800);
		$amount = str_replace('.', ',', $order['orderData']['priceWithDispatchMethod']);
		$email = $order['orderData']['contactData']['email'];

		$settings = $this->registry->core->loadModuleSettings('paybynet', Helper::getViewId());

		$query = array(
			'id_client' => $settings['id_client'],
			'id_trans' => $idTrans,
			'date_valid' => $dateValid,
			'amount' => $amount,
			'currency' => 'PLN',
			'email' => $email,
			'account' => $settings['accname_ac'],
			'accname' => $settings['accname_nm'] . '^NM^' .
					$settings['accname_zp'] . '^ZP^' .
					$settings['accname_ci'] . '^CI^' .
					$settings['accname_st'] . '^ST^' .
					$settings['accname_ct'] . '^CT^',
			'backpage' => $this->registry->router->generate('frontend.payment', true, array(
				'action' => 'confirm',
				'param' => 'paybynet'
			)),
			'backpagereject' => $this->registry->router->generate('frontend.payment', true, array(
				'action' => 'cancel',
				'param' => 'paybynet'
			)),
		);

		$data = '';
		foreach ($query as $key => $val)
		{
			$data .= sprintf('<%s>%s</%s>', $key, $val, $key);
		}

		$password = '<password>' . $settings['password'] . '</password>';

		$hash = '<hash>' . sha1($data . $password) . '</hash>';


		return array(
			'url' => self::URL,
			'hashtrans' => base64_encode($data . $hash)
		);
	}

	public function cancelPayment ()
	{
	}

	public function confirmPayment ()
	{
	}

	public function reportPayment ()
	{
	}

	public function checkPaymentStatus ($event, $request)
	{
		$sql = "SELECT idpaymentmethod FROM paymentmethod WHERE controller = 'paybynet' AND active=1";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();

		$rs = $stmt->fetch();

		if (!$rs) {
			return;
		}

		$settings = $this->registry->core->loadModuleSettings('paybynet', Helper::getViewId());

		if (empty($settings)) {
			return;
		}

		$id = $request['id'];

		$sql = "SELECT
				idorder
			FROM
				`order`
			WHERE
				idorder = :id
			AND
				paymentmethodid = :paymentmethodid
			AND
				orderstatusid = :positiveorderstatusid";

		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('idpaymentmethod', $rs['idpaymentmethod']);
		$stmt->bindValue('positiveorderstatusid', $settings['positiveorderstatusid']);
		$stmt->execute();

		if ($stmt->fetch()) {
			return;
		}

		try {
			$soap = new \SoapClient(self::WSDL);
			$statusCode = $soap->getStatusByPaymentID($id, $settings['id_client']);
		}
		catch(\SoapFault $e) {
			throw new CoreException('Nie można połączyć się z pbn.paybynet.com.pl');
		}

		$status = 0;

		if ($statusCode >= 1000 && $statusCode <= 1012) {
			$status = $settings['verificationrejectedorderstatusid'];
			$comment = 'Weryfikacja odrzucona';
		}
		else if ($statusCode == 2303) {
			$status = $settings['positiveorderstatusid'];
			$comment = 'Płatność zakończona sukcesem';
		}
		else if ($statusCode == 2301 || $statusCode == 2302){
			$status = $settings['negativeorderstatusid'];
			$comment = 'Płatność zakończona niepowodzeniem';
		}

		if ($status == 0){
			return;
		}

		$sql = "UPDATE `order` SET orderstatusid = :status WHERE idorder = :idorder";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('status', $status);
		$stmt->bindValue('idorder', $id);
		$stmt->execute();

		$sql = 'INSERT INTO orderhistory SET
					content = :content,
					orderstatusid = :status,
					orderid = :idorder,
					inform = 0';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('content', $comment);
		$stmt->bindValue('idorder', $id);
		$stmt->bindValue('status', $status);
		$stmt->execute();
	}

}