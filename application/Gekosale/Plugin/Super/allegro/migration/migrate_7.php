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
namespace Gekosale\Super\Allegro;

class Migrate_7 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('DROP TABLE IF EXISTS `allegrooptionstemplate`', array());
		$this->execSql('DROP TABLE IF EXISTS `allegroquantityoption`', array());
		$this->execSql('DROP TABLE IF EXISTS `allegrokindofqty`', array());
		$this->execSql('DROP TABLE IF EXISTS `allegrokindofdescription`', array());
		$this->execSql('DROP TABLE IF EXISTS `allegrosaleformat`', array());
		$this->execSql('DROP TABLE IF EXISTS `allegrodispatchmethodtemplate`', array());
		$this->execSql('DROP TABLE IF EXISTS `allegroauction`', array());
		$this->execSql('CREATE  TABLE `auction` (
					  `idauction` INT NOT NULL AUTO_INCREMENT,
					  `itemid` INT UNSIGNED NULL DEFAULT NULL,
					  `idproduct` INT UNSIGNED NOT NULL,
					  `title` VARCHAR(50) NOT NULL,
					  `variant` INT UNSIGNED NULL DEFAULT NULL,
					  `description` TEXT NOT NULL,
					  `quantity` INT UNSIGNED NOT NULL,
					  `category` INT UNSIGNED NOT NULL,
					  `minprice` DECIMAL(15,2) NOT NULL,
					  `buyprice` DECIMAL(15,2) NOT NULL,
					  `startprice` DECIMAL(15,2) NOT NULL,
					  `startdate` DATE NOT NULL,
					  `enddate` DATE NOT NULL,
					  PRIMARY KEY (`idauction`) ) ENGINE = InnoDB', array());
	}

	public function down ()
	{
	}
}