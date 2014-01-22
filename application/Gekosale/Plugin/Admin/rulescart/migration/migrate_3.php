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
namespace Gekosale\Admin\RulesCart;

class Migrate_3 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('ALTER TABLE `rulescart` DROP COLUMN `name`', array());
		
		$this->execSql('CREATE TABLE `rulescarttranslation` (
						  `idrulescarttranslation` int(10) unsigned NOT NULL AUTO_INCREMENT,
						  `name` varchar(45) NOT NULL,
						  `description` TEXT,
						  `languageid` int(10) unsigned DEFAULT NULL,
						  `rulescartid` int(10) unsigned NOT NULL,
						  PRIMARY KEY (`idrulescarttranslation`),
						  UNIQUE KEY `UNIQUE_rulescarttranslation_name_languageid` (`name`,`languageid`),
						  KEY `FK_rulescarttranslation_languageid` (`languageid`),
						  KEY `FK_rulescarttranslation_rulescartid` (`rulescartid`),
						  CONSTRAINT `FK_rulescarttranslation_languageid` FOREIGN KEY (`languageid`) REFERENCES `language` (`idlanguage`) ON DELETE CASCADE ON UPDATE NO ACTION,
						  CONSTRAINT `FK_rulescarttranslation_rulescartid` FOREIGN KEY (`rulescartid`) REFERENCES `rulescart` (`idrulescart`) ON DELETE CASCADE ON UPDATE NO ACTION
						) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC', array());
	}

	public function down ()
	{
		$this->execSql('DROP TABLE rulescarttranslation', array());
	}
} 