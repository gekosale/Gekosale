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

use SoapClient;

class KurjerzyApiModel extends Component\Model
{
	/**
	 * @todo
	 */
	const KURJERZY_API = 'https://www.kurjerzy.pl/webservice/wellcommerce';

	const WC_API_KEY = '59a268f08f575f7659474c778525f640';
	const WC_API_PIN = '495488795621';

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
		$this->instance = new Instance();
		$this->paymentSettings = $this->instance->getPaymentSettings();
		$this->client = $this->instance->getClient();
		$this->soapClient = new SoapClient(NULL, array(
			'location' => self::KURJERZY_API,
			'uri'      => 'adres_klienta_api'
		));
	}

	protected function getHash ($data, $pin) {
		return md5(md5(serialize($data)).$pin);
	}

	public function registerClient ()
	{
		if (! $this->testAccess()){
			return Array(
				'error' => true,
				'errorMsg' => 'Nie udało się połączyć z serwerem KurJerzy.'
			);
		}

		return $this->registerCompany();
	}

	public function testAccess ()
	{
		return $this->soapClient->getUserProducts(self::WC_API_KEY, $this->getHash(array(), self::WC_API_PIN), array());
	}

	public function checkClientNip () {}

	public function registerCompany ()
	{
		$password = Core::passwordGenerate();

		$userData = array(
			'userEmail'    => $this->client['result']['client']['email'],
			'userPassword' => $password,
			'userName'     => $this->client['result']['client']['firstname'],
			'userLastName' => $this->client['result']['client']['surname'],
			'userCompany'  => $this->client['result']['client']['companyname'],
			'userCity'     => $this->client['result']['client']['city'],
			'userStreet'   => $this->client['result']['client']['street'],
			'userPostCode' => $this->client['result']['client']['postcode'],
			'userHouseNr'  => $this->client['result']['client']['streetno'],
			'userFlatNr'   => $this->client['result']['client']['placeno'],

			/**
			 * @todo Back Connect
			 */
			'userShopUrl'  => App::getURL()
		);


		return $this->soapClient->registerCustomer(self::WC_API_KEY, $this->getHash($userData, self::WC_API_PIN), $userData) + array(
			'login' => $this->client['result']['client']['email'],
			'passwd' => $password,			
		);
	}

	public function enableModule () {}
}