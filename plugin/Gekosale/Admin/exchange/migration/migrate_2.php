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

class Migrate_2 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('
CREATE TABLE `importperiod` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NULL DEFAULT NULL,
	`pattern` TEXT NULL,
	`categoryseparator` VARCHAR(50) NULL DEFAULT NULL,
	`source` TINYINT(4) NULL DEFAULT NULL,
	`url` VARCHAR(255) NULL DEFAULT NULL,
	`username` VARCHAR(50) NULL DEFAULT NULL,
	`password` VARCHAR(50) NULL DEFAULT NULL,
	`interval` INT(11) NULL DEFAULT NULL,
	`adddate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COLLATE=\'utf8_general_ci\'
ENGINE=InnoDB;

', array());
	}

	public function down ()
	{
		$this->execSql('DROP TABLE `importperiod`', array());
	}
} 