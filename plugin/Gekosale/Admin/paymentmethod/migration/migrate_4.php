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
*/
namespace Gekosale\Admin\PaymentMethod;

class Migrate_4 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql("INSERT INTO `event` (`idevent`, `name`, `model`, `method`, `module`, `hierarchy`) VALUES (NULL, 'admin.paymentmethod.initForm', 'payment/eraty', 'getPaymentMethodConfigurationForm', 'Gekosale', 0);", array());
		$this->execSql("INSERT INTO `event` (`idevent`, `name`, `model`, `method`, `module`, `hierarchy`) VALUES (NULL, 'admin.paymentmethod.model.save', 'payment/eraty', 'saveSettings', 'Gekosale', 0);", array());
		$this->execSql("INSERT INTO `paymentmethodview` (`idpaymentmethodview`, `paymentmethodid`, `viewid`, `adddate`) VALUES (NULL, (SELECT `idpaymentmethod` FROM `paymentmethod` WHERE `controller` = 'eraty' ), 3, NOW());", array());
	}

	public function down ()
	{
		$this->execSql("DELETE FROM `event` WHERE `module` = 'payment/eraty'", array());
	}
}