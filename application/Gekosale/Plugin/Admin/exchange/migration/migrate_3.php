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

class Migrate_3 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('ALTER TABLE `importxml` CHANGE COLUMN `id` `idimportxml` INT(10) NOT NULL AUTO_INCREMENT FIRST', array());
		$this->execSql('ALTER TABLE `importperiod` ADD COLUMN `lastdate` TIMESTAMP NULL DEFAULT NULL AFTER `adddate`', array());
		$this->execSql('ALTER TABLE `importperiod` CHANGE COLUMN `id` `idimportperiod` INT(10) NOT NULL AUTO_INCREMENT FIRST', array());
		$this->execSql('ALTER TABLE `importperiod` ADD UNIQUE INDEX `name` (`name`)', array());

		$this->execSql('CREATE TABLE `exportxml` (
	`idexportxml` INT(10) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NULL DEFAULT NULL,
	`pattern` TEXT NULL,
	`type` INT NULL DEFAULT NULL,
	`adddate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`lastdate` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`idexportxml`),
	UNIQUE INDEX `name` (`name`)
)
COLLATE=\'utf8_general_ci\'
ENGINE=InnoDB', array());

	}

	public function down ()
	{
	}
}