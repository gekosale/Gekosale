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
namespace Gekosale\Admin\Sendingo;

class Migrate_1 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql("INSERT INTO `event` (`name`, `model`, `method`, `module`, `hierarchy`) VALUES ('admin.view.initForm', 'sendingo', 'addFields', 'Gekosale', 0)", array());
		$this->execSql("INSERT INTO `event` (`name`, `model`, `method`, `module`, `hierarchy`) VALUES ('admin.view.model.save', 'sendingo', 'saveSettings', 'Gekosale', 0)", array());
		$this->execSql("ALTER TABLE `view` ADD COLUMN `sendingo` VARCHAR(255) NULL", array());
		$this->execSql('INSERT INTO controller SET name = \'sendingo\', version = 1, description = \'Sendingo\', enable = 1, mode = 1', array());
		$this->execSql('INSERT INTO `right` SET controllerid = (SELECT idcontroller FROM controller WHERE name = \'sendingo\'), groupid = 1, permission = 127', array());
	}

	public function down ()
	{

	}
}