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
namespace Gekosale\Admin\Availablity;

class Migrate_1 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('INSERT INTO controller SET name = \'availablity\', version = 1, description = \'Dostępnosc\', enable = 1, mode = 1', array());
		$this->execSql('INSERT INTO `right` SET controllerid = (SELECT idcontroller FROM controller WHERE name = \'availablity\'), groupid = 1, permission = 127', array());
		$this->execSql('CREATE TABLE `availablity` (
						  `idavailablity` int(10) unsigned NOT NULL AUTO_INCREMENT,
						  `adddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
						  PRIMARY KEY (`idavailablity`)
						) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8', array());
		$this->execSql('CREATE TABLE `availablitytranslation` (
						  `idavailablitytranslation` int(10) unsigned NOT NULL AUTO_INCREMENT,
						  `name` varchar(128) NOT NULL,
						  `description` varchar(3000) DEFAULT NULL,
						  `availablityid` int(10) unsigned DEFAULT NULL,
						  `languageid` int(10) unsigned DEFAULT NULL,
						  PRIMARY KEY (`idavailablitytranslation`),
						  KEY `FK_availablitytranslation_availablityid` (`availablityid`),
						  KEY `FK_availablitytranslation_languageid` (`languageid`),
						  CONSTRAINT `FK_availablitytranslation_availablityid` FOREIGN KEY (`availablityid`) REFERENCES `availablity` (`idavailablity`),
						  CONSTRAINT `FK_availablitytranslation_languageid` FOREIGN KEY (`languageid`) REFERENCES `language` (`idlanguage`)
						) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8', array());
		$this->execSql('ALTER TABLE `product` ADD `availablityid` INT( 11 ) NULL DEFAULT NULL', array());
	}

	public function down ()
	{
	}
} 