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
namespace Gekosale\Admin\Exchangexml;

class Migrate_2 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql("ALTER TABLE `importxml`	ADD COLUMN `attributes` TEXT NULL DEFAULT NULL AFTER `paymentmethodname`", array());
		$this->execSql("ALTER TABLE `importxml` ADD COLUMN `migrateid` VARCHAR(255) NOT NULL AFTER `idimportxml`", array());
		$this->execSql("ALTER TABLE `exchange` CHANGE COLUMN `url` `url` VARCHAR(255) NULL DEFAULT NULL AFTER `source`", array());
		$this->execSql("ALTER TABLE `exchange` ADD COLUMN `categoryseparator` VARCHAR(32) NULL AFTER `url`", array());
	}

	public function down ()
	{
	}
}