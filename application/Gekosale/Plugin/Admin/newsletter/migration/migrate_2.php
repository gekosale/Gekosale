<?php

namespace Gekosale\Admin\Newsletter;

class Migrate_2 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql("ALTER TABLE `newsletter` CHANGE COLUMN `htmlform` `htmlform` LONGTEXT NOT NULL  , CHANGE COLUMN `textform` `textform` TEXT NOT NULL", array());
	}

	public function down ()
	{

	}
}