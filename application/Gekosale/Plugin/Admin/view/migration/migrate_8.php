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
namespace Gekosale\Admin\View;

class Migrate_8 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('ALTER TABLE `event` ADD COLUMN `mode` TINYINT(1) NULL DEFAULT 1  AFTER `hierarchy`', array());
		$this->execSql('UPDATE event SET mode = 0 WHERE idevent IN (11, 12)', array());
	}

	public function down ()
	{
	}
}