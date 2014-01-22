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
namespace Gekosale\Plugin;

use SoapClient;

class Przelewy24ApiModel extends Component\Model
{
	const P24_API_WSDL = 'https://secure.przelewy24.pl/external/wsdl/service.php?wsdl';

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
		$this->instance = new Instance();
		$this->paymentSettings = $this->instance->getPaymentSettings();
		$this->client = $this->instance->getClient();
		$this->soapClient = new SoapClient(self::P24_API_WSDL);
	}

	public function registerClient ()
	{
		if (! $this->testAccess()){
			return Array(
				'error' => true,
				'errorMsg' => 'Nie udało się połączyć z serwerem Przelewy24.'
			);
		}
		else{
			$checkNip = $this->checkClientNip();
			
			if (isset($checkNip['error'])){
				return $checkNip;
			}
			else{
				return $this->registerCompany();
			}
		}
	}

	public function testAccess ()
	{
		return $this->soapClient->TestAccess($this->paymentSettings['result']['idsprzedawcy'], $this->paymentSettings['result']['apikey']);
	}

	public function checkClientNip ()
	{
		$res = $this->soapClient->CheckNIP($this->paymentSettings['result']['idsprzedawcy'], $this->paymentSettings['result']['apikey'], $this->client['result']['client']['nip']);
		if ($res->error->errorCode){
			return Array(
				'error' => true,
				'errorMsg' => $res->error->errorMessage
			);
		}
	}

	public function registerCompany ()
	{
		$company = array(
			"companyName" => $this->client['result']['client']['companyname'],
			"city" => $this->client['result']['client']['city'],
			"street" => $this->client['result']['client']['street'] . ' ' . $this->client['result']['client']['streetno'] . (($this->client['result']['client']['placeno'] != '') ? '/' . $this->client['result']['client']['placeno'] : ''),
			"postCode" => $this->client['result']['client']['postcode'],
			"email" => $this->client['result']['client']['email'],
			"nip" => $this->client['result']['client']['nip'],
			"person" => $this->client['result']['client']['firstname'] . ' ' . $this->client['result']['client']['surname'],
			"regon" => $this->client['result']['client']['regon'],
			"acceptance" => false,
			"IBAN" => ""
		);
		
		$res = $this->soapClient->CompanyRegister($this->paymentSettings['result']['idsprzedawcy'], $this->paymentSettings['result']['apikey'], $company);
		
		return $res;
	}

	public function enableModule ()
	{
		$sql = "SELECT name FROM paymentmethod WHERE controller='platnosci'";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$rs = $stmt->fetch();

		if(empty($rs)) {
			$sql = "INSERT INTO paymentmethod SET name='Platnosci.pl', controller='platnosci', online=1, active=1";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->execute();
		}
	}
}