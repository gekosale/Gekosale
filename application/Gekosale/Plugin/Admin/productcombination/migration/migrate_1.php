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
namespace Gekosale\Admin\Productcombination;

class Migrate_1 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql("
		CREATE TABLE `combination` (
		  `idcombination` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(128) NOT NULL,
		  `value` decimal(16,2) unsigned DEFAULT '100.00',
		  `suffixtypeid` int(10) unsigned DEFAULT '1',
		  `description` text,
		  `shortdescription` varchar(1000) DEFAULT NULL,
		  `photoid` int(10) unsigned DEFAULT NULL,
		  PRIMARY KEY (`idcombination`),
		  KEY `FK_combination_suffixtypeid` (`suffixtypeid`),
		  KEY `FK_combination_photoid` (`photoid`),
		  CONSTRAINT `FK_combination_photoid` FOREIGN KEY (`photoid`) REFERENCES `file` (`idfile`),
		  CONSTRAINT `FK_combination_suffixtypeid` FOREIGN KEY (`suffixtypeid`) REFERENCES `suffixtype` (`idsuffixtype`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8", array());
		
		$this->execSql('
			CREATE TABLE `combinationtranslation` (
		  `idcombinationtranslation` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(128) NOT NULL,
		  `description` text,
		  `shortdescription` varchar(1000) DEFAULT NULL,
		  `languageid` int(10) unsigned DEFAULT NULL,
		  `combinationid` int(10) unsigned DEFAULT NULL,
		  PRIMARY KEY (`idcombinationtranslation`),
		  KEY `FK_combinationtranslation_languageid` (`languageid`),
		  KEY `FK_combinationtranslation_combinationid` (`combinationid`),
		  CONSTRAINT `FK_combination_combinationid` FOREIGN KEY (`combinationid`) REFERENCES `combination` (`idcombination`),
		  CONSTRAINT `FK_combination_languageid` FOREIGN KEY (`languageid`) REFERENCES `language` (`idlanguage`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8', array());
		
		$this->execSql('
			CREATE TABLE `combinationview` (
		  `idcombinationview` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `combinationid` int(10) unsigned NOT NULL,
		  `viewid` int(10) unsigned NOT NULL,
		  PRIMARY KEY (`idcombinationview`),
		  KEY `FK_combinationview_combinationid` (`combinationid`),
		  KEY `FK_combinationview_viewid` (`viewid`),
		  CONSTRAINT `FK_combinationview_combinationid` FOREIGN KEY (`combinationid`) REFERENCES `combination` (`idcombination`),
		  CONSTRAINT `FK_combinationview_viewid` FOREIGN KEY (`viewid`) REFERENCES `view` (`idview`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8', array());
		
		$this->execSql('
			CREATE TABLE `productcombination` (
		  `idproductcombination` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `productid` int(10) unsigned NOT NULL,
		  `combinationid` int(10) unsigned NOT NULL,
		  `productattributesetid` int(10) unsigned DEFAULT NULL,
		  `numberofitems` int(10) unsigned NOT NULL,
		  PRIMARY KEY (`idproductcombination`),
		  UNIQUE KEY `UNIQUE_productcombination_productid_combinationid` (`productid`,`combinationid`),
		  KEY `FK_productcombination_combinationid` (`combinationid`),
		  KEY `FK_productcombination_productattributesetid` (`productattributesetid`),
		  CONSTRAINT `FK_productcombination_combinationid` FOREIGN KEY (`combinationid`) REFERENCES `combination` (`idcombination`),
		  CONSTRAINT `FK_productcombination_productattributesetid` FOREIGN KEY (`productattributesetid`) REFERENCES `productattributeset` (`idproductattributeset`),
		  CONSTRAINT `FK_productcombination_productid` FOREIGN KEY (`productid`) REFERENCES `product` (`idproduct`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8', array());
		
		$this->installController('productcombination', 'Zestawy produktów', 1, 1, 1);
	}

	public function down ()
	{
	}
} 