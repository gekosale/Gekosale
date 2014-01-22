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

class Migrate_6 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('CREATE TABLE `allegroauction` (
					  `idallegroauction` int(10) unsigned NOT NULL auto_increment,
					  `auctionnumber` int(10) unsigned NOT NULL,
					  `startingauctionprice` decimal(16,2) NOT NULL,
					  `priceqty` decimal(16,2) NOT NULL,
					  `startingprice` decimal(16,2) NOT NULL,
					  `buynow` decimal(16,2) NOT NULL,
					  `qtyphotos` int(10) unsigned NOT NULL,
					  `comment` varchar(450) NOT NULL,
					  `raportinfo` int(10) unsigned NOT NULL,
					  `futureauctionstatus` varchar(450) NOT NULL,
					  `qtyviewed` int(10) unsigned NOT NULL,
					  `paymentauction` decimal(16,2) NOT NULL,
					  `qty` int(10) unsigned NOT NULL,
					  `startdate` datetime NOT NULL,
					  `enddate` datetime NOT NULL,
					  `buyerid` int(10) unsigned NOT NULL,
					  `auctionname` varchar(500) NOT NULL,
					  `manyoffert` int(10) unsigned NOT NULL,
					  `sellerid` int(10) unsigned NOT NULL,
					  `countryid` int(10) unsigned NOT NULL,
					  `additionaloptions` int(10) unsigned NOT NULL,
					  `maximalbuyeroffert` decimal(16,2) NOT NULL,
					  `maximaloffert` decimal(16,2) NOT NULL,
					  `qtyoffert` int(10) unsigned NOT NULL,
					  `qtyselloffert` int(10) unsigned NOT NULL,
					  `qtynotselloffert` int(10) unsigned NOT NULL,
					  `buyerlogin` varchar(450) NOT NULL,
					  `qtypointsbuyer` int(10) unsigned NOT NULL,
					  `buyercountry` varchar(450) NOT NULL,
					  `sellerlogin` varchar(450) NOT NULL,
					  `qtypointsseller` int(10) unsigned NOT NULL,
					  `sellercountry` varchar(450) NOT NULL,
					  `qtywatching` int(10) unsigned NOT NULL,
					  `buynowstatus` int(10) unsigned NOT NULL,
					  `commentauction` varchar(450) NOT NULL,
					  `finishauctioninfo` int(10) unsigned NOT NULL,
					  `provision` decimal(16,2) NOT NULL,
					  `viewid` int(10) unsigned default NULL,
					  PRIMARY KEY  (`idallegroauction`),
					  KEY `FK_allegroauction_viewid` (`viewid`),
					  CONSTRAINT `FK_allegroauction_viewid` FOREIGN KEY (`viewid`) REFERENCES `view` (`idview`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;', array());
		
		$this->execSql("CREATE TABLE `allegrodispatchmethodtemplate` (
					  `idallegrodispatchmethodtemplate` int(10) unsigned NOT NULL auto_increment,
					  `name` varchar(45) NOT NULL,
					  `adddate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
					  `viewid` int(10) unsigned default NULL,
					  PRIMARY KEY  (`idallegrodispatchmethodtemplate`),
					  KEY `FK_allegrodispatchmethodtemplate_viewid` (`viewid`),
					  CONSTRAINT `FK_allegrodispatchmethodtemplate_viewid` FOREIGN KEY (`viewid`) REFERENCES `view` (`idview`)
					) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;", array());

		$this->execSql("CREATE TABLE `allegrokindofdescription` (
					  `idallegrokindofdescription` int(10) unsigned NOT NULL auto_increment,
					  `name` varchar(45) NOT NULL,
					  `info` varchar(45) NOT NULL,
					  `viewid` int(10) unsigned default NULL,
					  PRIMARY KEY  (`idallegrokindofdescription`),
					  KEY `FK_allegrokindofdescription_viewid` (`viewid`),
					  CONSTRAINT `FK_allegrokindofdescription_viewid` FOREIGN KEY (`viewid`) REFERENCES `view` (`idview`)
					) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;", array());
		
		$this->execSql("INSERT INTO `allegrokindofdescription` VALUES (1,'TXT_INSERT_DESCRIPTION_FROM_SHOP','Wstaw opis produktu ze sklepu',NULL),(2,'TXT_INSERT_DESCRIPTION_HENDWRITING','Wstaw opis produktu ręcznie',NULL);", array());
		
		$this->execSql("CREATE TABLE `allegrokindofqty` (
					  `idallegrokindofqty` int(10) unsigned NOT NULL auto_increment,
					  `name` varchar(45) NOT NULL,
					  `info` varchar(45) default NULL,
					  `viewid` int(10) unsigned default NULL,
					  PRIMARY KEY  (`idallegrokindofqty`),
					  KEY `FK_allegrokindofqty_viewid` (`viewid`),
					  CONSTRAINT `FK_allegrokindofqty_viewid` FOREIGN KEY (`viewid`) REFERENCES `view` (`idview`)
					) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;", array());
		
		$this->execSql("INSERT INTO `allegrokindofqty` VALUES (1,'TXT_CHOOSE_PIECES','sztuki',NULL),(2,'TXT_CHOOSE_SETS','zestawy',NULL),(3,'TXT_CHOOSE_PAIRS','pary',NULL);", array());

		$this->execSql("CREATE TABLE `allegroquantityoption` (
					  `idallegroquantityoption` int(10) unsigned NOT NULL auto_increment,
					  `name` varchar(45) NOT NULL,
					  `info` varchar(45) default NULL,
					  `viewid` int(10) unsigned default NULL,
					  PRIMARY KEY  (`idallegroquantityoption`),
					  KEY `FK_allegroquantityoption_viewid` (`viewid`),
					  CONSTRAINT `FK_allegroquantityoption_viewid` FOREIGN KEY (`viewid`) REFERENCES `view` (`idview`)
					) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;", array());
		
		$this->execSql("INSERT INTO `allegroquantityoption` VALUES (1,'TXT_DISPLAY_ALL_ITEMS','wystaw wszystkie przedmioty ze sklepu',NULL),(2,'TXT_DISPLAY_ONLY','wystaw tylko wybraną ilość sztuk',NULL),(3,'TXT_DISPLAY_ONLY_PERCENTAGE','wystaw % stanu magazynowego produktu, zaokrąg',NULL),(4,'TXT_LEAVE_QTY','pozostaw w magazynie wybraną ilość sztuk prze',NULL),(5,'TXT_LEAVE_PERCENTAGE','pozostaw w magazynie % stanu magazynowego pro',NULL);", array());
		
		$this->execSql("CREATE TABLE `allegrosaleformat` (
					  `idallegrosaleformat` int(10) unsigned NOT NULL auto_increment,
					  `name` varchar(45) NOT NULL,
					  `info` varchar(45) default NULL,
					  `viewid` int(10) unsigned default NULL,
					  PRIMARY KEY  (`idallegrosaleformat`),
					  KEY `FK_allegrosaleformat_viewid` (`viewid`),
					  CONSTRAINT `FK_allegrosaleformat_viewid` FOREIGN KEY (`viewid`) REFERENCES `view` (`idview`)
					) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;", array());
		
		$this->execSql("INSERT INTO `allegrosaleformat` VALUES (1,'TXT_ONLY_BUY_NOW','Opcja Kup Teraz!',NULL),(2,'TXT_AUCTION','Aukcja',NULL);", array());
		
		$this->execSql("CREATE TABLE `allegrooptionstemplate` (
					  `idallegrooptionstemplate` int(10) unsigned NOT NULL auto_increment,
					  `name` varchar(50) NOT NULL,
					  `titleformat` varchar(80) NOT NULL,
					  `allegrokindofdescriptionid` int(10) unsigned NOT NULL default '1',
					  `description` varchar(3000) default NULL,
					  `watermark` int(1) unsigned default '0',
					  `thumb` int(1) unsigned default '0',
					  `photoid` int(10) unsigned default NULL,
					  `allegrosaleformatid` int(10) unsigned NOT NULL default '1',
					  `startingprice` decimal(10,2) default NULL,
					  `buynowprice` decimal(10,2) default NULL,
					  `calculatebuynow` int(1) unsigned NOT NULL default '0',
					  `calculatebuynowsuffix` int(1) unsigned NOT NULL default '0',
					  `calculatebuynowvalue` decimal(10,2) default NULL,
					  `calculatestartingprice` int(1) unsigned default NULL,
					  `startingpricesuffix` int(1) unsigned NOT NULL,
					  `allegrokindofqtyid` int(10) unsigned NOT NULL,
					  `allegroquantityoptionid` int(10) unsigned NOT NULL,
					  `dateoption` int(1) unsigned NOT NULL,
					  `date` datetime default NULL,
					  `duration` int(10) unsigned NOT NULL,
					  `renewable` int(1) unsigned default '0',
					  `storagelimit` int(1) unsigned NOT NULL default '0',
					  `shipmentcostcovered` int(1) unsigned NOT NULL default '1',
					  `allegrodispatchmethodtemplateid` int(10) unsigned default NULL,
					  `boldtitle` int(1) unsigned default '0',
					  `highlight` int(1) unsigned default '0',
					  `ekooffer` int(1) unsigned default '0',
					  `difference` int(1) unsigned default '0',
					  `categorypage` int(1) unsigned default '0',
					  `mainpage` int(1) unsigned default '0',
					  `adddate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
					  `calculatestartingpricevalue` decimal(10,2) default NULL,
					  `auctionwithbuynow` int(2) unsigned NOT NULL default '0',
					  `qty` int(10) unsigned NOT NULL,
					  `allegrooriginalcategoryid` int(10) unsigned default NULL,
					  `choosepriceformat` int(10) unsigned default NULL,
					  `choosepriceauctionwithbuynow` int(10) unsigned default NULL,
					  `choosepriceauction` int(10) unsigned default NULL,
					  `calculateauctionbuynow` int(10) unsigned default NULL,
					  `calculateauctionbuynowsuffix` int(10) unsigned default NULL,
					  `calculateauctionbuynowvalue` decimal(10,2) default NULL,
					  `auctionbuynowprice` decimal(10,2) default NULL,
					  `productpricebuynow` int(10) unsigned NOT NULL default '0',
					  `productpriceauction` int(10) unsigned NOT NULL default '0',
					  `storeid` int(10) unsigned default NULL,
					  `viewid` int(10) unsigned default NULL,
					  `parentid` int(10) unsigned default NULL,
					  PRIMARY KEY  USING BTREE (`idallegrooptionstemplate`),
					  KEY `FK_allegrooptionstemplate_allegrokindofdescriptionid` (`allegrokindofdescriptionid`),
					  KEY `FK_allegrooptionstemplate_allegrosaleformatid` (`allegrosaleformatid`),
					  KEY `FK_allegrooptionstemplate_allegrokindofqtyid` (`allegrokindofqtyid`),
					  KEY `FK_allegrooptionstemplate_allegroquantityoptionid` (`allegroquantityoptionid`),
					  KEY `FK_allegrooptionstemplate_allegrodispatchmethodtemplateid` (`allegrodispatchmethodtemplateid`),
					  KEY `FK_allegrooptionstemplate_viewid` (`viewid`),
					  CONSTRAINT `FK_allegrooptionstemplate_allegrodispatchmethodtemplateid` FOREIGN KEY (`allegrodispatchmethodtemplateid`) REFERENCES `allegrodispatchmethodtemplate` (`idallegrodispatchmethodtemplate`),
					  CONSTRAINT `FK_allegrooptionstemplate_allegrokindofdescriptionid` FOREIGN KEY (`allegrokindofdescriptionid`) REFERENCES `allegrokindofdescription` (`idallegrokindofdescription`),
					  CONSTRAINT `FK_allegrooptionstemplate_allegrokindofqtyid` FOREIGN KEY (`allegrokindofqtyid`) REFERENCES `allegrokindofqty` (`idallegrokindofqty`),
					  CONSTRAINT `FK_allegrooptionstemplate_allegroquantityoptionid` FOREIGN KEY (`allegroquantityoptionid`) REFERENCES `allegroquantityoption` (`idallegroquantityoption`),
					  CONSTRAINT `FK_allegrooptionstemplate_allegrosaleformatid` FOREIGN KEY (`allegrosaleformatid`) REFERENCES `allegrosaleformat` (`idallegrosaleformat`),
					  CONSTRAINT `FK_allegrooptionstemplate_viewid` FOREIGN KEY (`viewid`) REFERENCES `view` (`idview`)
					) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;", array());
	}

	public function down ()
	{
	}
}