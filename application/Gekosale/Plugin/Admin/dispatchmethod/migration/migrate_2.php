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
namespace Gekosale\Admin\DispatchMethod;

class Migrate_2 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql("ALTER TABLE `dispatchmethodprice` CHANGE COLUMN `dispatchmethodcost` `dispatchmethodcost` DECIMAL(16,4) NOT NULL DEFAULT '0.00'", array());
		$this->execSql("ALTER TABLE `dispatchmethodweight` CHANGE COLUMN `cost` `cost` DECIMAL(16,4) NOT NULL", array());
	}

	public function down ()
	{
	}
} 