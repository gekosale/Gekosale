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
namespace Gekosale\Admin\Newsletter;

class Migrate_1 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql("ALTER TABLE `clientnewsletter` ADD COLUMN `sendingoid` INT UNSIGNED NULL DEFAULT NULL  AFTER `inactivelink`", array());
		$this->execSql("ALTER TABLE `clientnewsletter` DROP INDEX `UNIQUE_clientnewsletter_email`, ADD UNIQUE INDEX `UNIQUE_clientnewsletter_email_viewid` (`email` ASC, `viewid` ASC)", array());
		$this->execSql("ALTER TABLE `clientdata` DROP COLUMN `newsletter`", array());
	}

	public function down ()
	{

	}
}