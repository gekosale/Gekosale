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
 * $Revision: 114 $
 * $Author: gekosale $
 * $Date: 2011-05-07 18:41:26 +0200 (So, 07 maj 2011) $
 * $Id: store.php 114 2011-05-07 16:41:26Z gekosale $
 */
namespace Gekosale;

use Exception;

class FurgonetkaApiModel extends Component\Model
{
	protected $model;

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
		$this->instance = new Instance();
		$this->paymentSettings = $this->instance->getPaymentSettings();
		$this->client = $this->instance->getClient();
		$this->model = App::getModel('shipment/furgonetka');
	}

	public function getCaptcha()
	{
		try {
			$xml = $this->model->doRequest('generateCaptcha');
			return array(
				(string) $xml->token,
				'<img src="data:image/gif;base64,' . (string) $xml->image . '" alt=""/>'
			);
		}
		catch(Exception $e) {
			return FALSE;
		}
	}

	public function registerCompany ($token, $code)
	{
		$password = Core::passwordGenerate();

		$userData = array (
			'token' => $token,
			'code' => $code,
			'account_type' => 'company',
			'email' => $this->client['result']['client']['email'],
			'password' => $password,
			'password2' => $password,
			'name' => $this->client['result']['client']['firstname'],
			'surname' => $this->client['result']['client']['surname'],
			'nip' => $this->client['result']['client']['nip'],
			'street' => $this->client['result']['client']['street'] . ' ' . $this->client['result']['client']['streetno'] . ($this->client['result']['client']['placeno'] ? '/' . $this->client['result']['client']['placeno'] : ''),
			'city' => $this->client['result']['client']['city'],
			'postcode' => $this->client['result']['client']['postcode'],
			'phone' => $this->client['result']['client']['phone'],
			// @todo
			'iban' => '11222233334444555566667777',
			'company' => $this->client['result']['client']['companyname'],
			'regulamin' => true,
			'commercial' => true,
		);

		try
		{
			$this->model->doRequest('registerUser', $userData);

			return array(
				'login' => $this->client['result']['client']['email'],
				'password' => $password
			);
		}
		catch(Exception $e) {}

		return FALSE;
	}
}