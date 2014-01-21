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
namespace Gekosale\Admin\Exchange;

class Migrate_1 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('
CREATE TABLE `importxml` (
	`id` INT(10) NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`url` VARCHAR(255) NULL,
	`categories` VARCHAR(255) NULL,
	`price` DECIMAL(16,4) NOT NULL,
	`weight` DECIMAL(16,3) NULL,
	`stock` INT(11) NULL,
	`imageurl` VARCHAR(255) NULL,
	`description` TEXT NULL,
	`producer` VARCHAR(255) NULL,
	`ean` VARCHAR(30) NULL,
	`avail` INT(11) NULL,
	PRIMARY KEY (`id`)
)
COLLATE=\'utf8_general_ci\'
ENGINE=InnoDB
', array());
	}

	public function down ()
	{
		$this->execSql('DROP TABLE `importxml`', array());
	}
} 