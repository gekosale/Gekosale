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

class SendingoApiModel extends Component\Model
{
	protected $instanceId;
	protected $sendingoModel;
	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
		$this->instance = new Instance();
		$this->client = $this->instance->getClient();
		$instance = $this->instance->getInstance();
		$this->instanceId = (int)$instance['instance']['id'];
		
		$this->sendingoModel = App::getModel('sendingo');
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
		return true;
	}

	public function checkClientNip () {}

	public function registerCompany ()
	{
		$userData = array(
			'user' => array(
				'name'     => $this->client['result']['client']['firstname'],
				'surname'  => $this->client['result']['client']['surname'],
				'username' => 'wellcommerce_' . $this->instanceId,
				'email'    => $this->client['result']['client']['email'],
				'company'  => $this->client['result']['client']['companyname'],
				'phone'    => $this->client['result']['client']['phone'],
				'terms_of_service' => 1
			)
		);

		$data = $this->sendingoModel->doRequest($userData, 'api/v1/users.json', 'post', 0);
		
		if (isset($data['authentication_token'])) {
			return array(
				'username' => $data['username'],
				'password' => $data['password'],
				'auth_token' => $data['authentication_token'],
				'groups' => ''
			);
		}
		
		return false;
	}

	public function enableModule () {}
}