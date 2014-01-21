<?php

namespace Gekosale\Admin\ContentCategory;

class Migrate_2 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('ALTER TABLE `contentcategorytranslation` CHANGE COLUMN `description` `description` LONGTEXT NULL DEFAULT NULL', array());
	}

	public function down ()
	{
	}
} 