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

class Migrate_2 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql("INSERT INTO `event` (`idevent`, `name`, `model`, `method`, `module`, `hierarchy`) VALUES (NULL, 'admin.paymentmethod.initForm', 'payment/dotpay', 'getPaymentMethodConfigurationForm', 'Gekosale', 0)", array());
		$this->execSql("INSERT INTO `event` (`idevent`, `name`, `model`, `method`, `module`, `hierarchy`) VALUES (NULL, 'admin.paymentmethod.model.save', 'payment/dotpay', 'saveSettings', 'Gekosale', 0)", array());
		$this->execSql("INSERT INTO `event` (`idevent`, `name`, `model`, `method`, `module`, `hierarchy`) VALUES (NULL, 'admin.paymentmethod.getPaymentMethods', 'payment/dotpay', 'getPaymentMethod', 'Gekosale', 0)", array());
		$this->execSql("INSERT INTO `paymentmethod` (`idpaymentmethod`, `name`, `adddate`, `controller`, `online`, `active`, `maximumamount`, `hierarchy`) VALUES (NULL, 'Dotpay', '2012-10-19 11:30:02', 'dotpay', 1, 0, NULL, 10)", array());
		$this->execSql("INSERT INTO `paymentmethodview` (`idpaymentmethodview`, `paymentmethodid`, `viewid`, `adddate`) VALUES (NULL, (SELECT `idpaymentmethod` FROM `paymentmethod` WHERE `name` = 'Dotpay'), 3, NOW())", array());
	}

	public function down ()
	{
		$this->execSql("DELETE FROM `event` WHERE `module` = 'payment/dotpay'", array());
	}
}