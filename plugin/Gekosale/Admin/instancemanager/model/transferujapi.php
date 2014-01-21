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

class TransferujApiModel extends Component\Model
{
	const TRANSFERUJ_API_URL = 'https://secure.transferuj.pl/api/faab7ead3846a2d5e99331f5d5e533b0/';

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
		$this->instance = new Instance();
		$this->paymentSettings = $this->instance->getPaymentSettings();
		$this->client = $this->instance->getClient();
	}

	protected function doRequest ($Data, $action)
	{
		$fields_string = '';
		
		foreach ($Data as $key => $value){
			$fields_string .= $key . '=' . urlencode($value) . '&';
		}
		rtrim($fields_string, '&');
		
		$ci = curl_init();
		curl_setopt($ci, CURLOPT_USERAGENT, 'WellCommerce API');
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ci, CURLOPT_TIMEOUT, 30);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ci, CURLOPT_HEADER, FALSE);
		curl_setopt($ci, CURLOPT_POST, TRUE);
		curl_setopt($ci, CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ci, CURLOPT_URL, self::TRANSFERUJ_API_URL . $action);
		$response = curl_exec($ci);
		curl_close($ci);
		return $response;
	}

	public function getUniqueCode ()
	{
		$code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 3));
		
		$request = $this->doRequest(Array(
			'code' => $code
		), 'register/check-code');
		
		return simplexml_load_string($request);
	}

	public function enableModule ()
	{
		$sql = "SELECT name FROM paymentmethod WHERE controller='transferuj'";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$rs = $stmt->fetch();

		if(empty($rs)) {
			$sql = "INSERT INTO paymentmethod SET name='Transferuj.pl', controller='transferuj', online=1, active=1";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->execute();
		}
	}
}