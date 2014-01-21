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

class Migrate_4 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('DROP TABLE IF EXISTS `importperiod`', array());
		$this->execSql('DROP TABLE IF EXISTS `importxml`', array());		
		$this->execSql('
CREATE TABLE `importxml` (
	`idimportxml` INT(10) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`url` VARCHAR(255) NULL DEFAULT NULL,
	`categories` VARCHAR(255) NULL DEFAULT NULL,
	`price` DECIMAL(16,4) NULL DEFAULT NULL,
	`weight` DECIMAL(16,3) NULL DEFAULT NULL,
	`stock` INT(11) NULL DEFAULT NULL,
	`imageurl` VARCHAR(255) NULL DEFAULT NULL,
	`description` TEXT NULL,
	`producer` VARCHAR(255) NULL DEFAULT NULL,
	`ean` VARCHAR(30) NULL DEFAULT NULL,
	`avail` INT(11) NULL DEFAULT NULL,
	`photo` VARCHAR(255) NULL DEFAULT NULL,
	`parent` VARCHAR(255) NULL DEFAULT NULL,
	`view` VARCHAR(255) NULL DEFAULT NULL,
	`email` VARCHAR(255) NULL DEFAULT NULL,
	`phone` VARCHAR(255) NULL DEFAULT NULL,
	`adddate` TIMESTAMP NULL DEFAULT NULL,
	`ordertotal` DECIMAL(16,4) NULL DEFAULT NULL,
	`firstname` VARCHAR(255) NULL DEFAULT NULL,
	`surname` VARCHAR(255) NULL DEFAULT NULL,
	`groupname` VARCHAR(255) NULL DEFAULT NULL,
	`shop` VARCHAR(255) NULL DEFAULT NULL,
	`globalprice` VARCHAR(255) NULL DEFAULT NULL,
	`dispatchmethodprice` VARCHAR(255) NULL DEFAULT NULL,
	`client` VARCHAR(255) NULL DEFAULT NULL,
	`orderstatusname` VARCHAR(255) NULL DEFAULT NULL,
	`dispatchmethodname` VARCHAR(255) NULL DEFAULT NULL,
	`paymentmethodname` VARCHAR(255) NULL DEFAULT NULL,
	PRIMARY KEY (`idimportxml`)
)
COLLATE=\'utf8_general_ci\'
ENGINE=InnoDB
', array());
		$this->execSql('DROP TABLE IF EXISTS `exchange`', array());
		$this->execSql('CREATE TABLE `exchange` (
	`idexchange` INT(10) NOT NULL AUTO_INCREMENT,
	`type` TINYINT NOT NULL,
	`datatype` TINYINT NULL DEFAULT NULL,
	`name` VARCHAR(255) NOT NULL,
	`pattern` TEXT NOT NULL,
	`source` VARCHAR(255) NULL DEFAULT NULL,
	`url` VARCHAR(50) NULL DEFAULT NULL,
	`username` VARCHAR(50) NULL DEFAULT NULL,
	`password` VARCHAR(50) NULL DEFAULT NULL,
	`lastprocessed` INT(10) NOT NULL,
	`periodically` TINYINT NULL,
	`interval` INT(10) NULL DEFAULT NULL,
	`adddate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`lastdate` TIMESTAMP NULL DEFAULT NULL,
	`log` TEXT NULL DEFAULT NULL,	
	`locked` TINYINT UNSIGNED NOT NULL DEFAULT \'0\',	
	PRIMARY KEY (`idexchange`)
)
COLLATE=\'utf8_general_ci\'
ENGINE=InnoDB
', array());

		$this->execSql('INSERT INTO controller SET name = \'exchangexml\', version = 1, description = \'Centrum wymiany danych XML\', enable = 1, mode = 1', array());
		$this->execSql('INSERT INTO `right` SET controllerid = (SELECT idcontroller FROM controller WHERE name = \'exchangexml\'), groupid = 1, permission = 127', array());
	}

	public function down ()
	{
	}
}