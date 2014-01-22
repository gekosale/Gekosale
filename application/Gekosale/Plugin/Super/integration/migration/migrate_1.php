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
namespace Gekosale\Super\Integration;

class Migrate_1 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('CREATE TABLE `categorydomodi` (
						  `idcategorydomodi` int(10) unsigned NOT NULL AUTO_INCREMENT,
						  `categoryid` int(10) unsigned NOT NULL,
						  `domodiid` int(10) unsigned NOT NULL,
						  PRIMARY KEY (`idcategorydomodi`),
						  KEY `FK_categorydomodi_categoryid` (`categoryid`),
						  CONSTRAINT `FK_categorydomodi_categoryid` FOREIGN KEY (`categoryid`) REFERENCES `category` (`idcategory`) ON DELETE CASCADE ON UPDATE NO ACTION
						) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8', array());
		
		$this->execSql('CREATE TABLE `domodi` (
						  `iddomodi` int(10) unsigned NOT NULL AUTO_INCREMENT,
						  `name` varchar(255) NOT NULL,
						  `idorginal` int(10) unsigned NOT NULL,
						  `parentorginalid` int(10) unsigned DEFAULT NULL,
						  PRIMARY KEY (`iddomodi`)
						) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8', array());
	}

	public function down ()
	{
	}
} 