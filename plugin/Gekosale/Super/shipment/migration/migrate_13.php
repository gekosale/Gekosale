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
namespace Gekosale\Super\Shipment;

class Migrate_13 extends \Gekosale\Component\Migration
{

	public function up (){
		
		$this->execSql('CREATE TABLE `shipments` (
						  `idshipments` int(11) NOT NULL AUTO_INCREMENT,
						  `orderid` int(11) unsigned NOT NULL,
						  `guid` varchar(128) NOT NULL,
						  `packagenumber` varchar(128) NOT NULL,
						  `label` longblob,
						  `adddate` datetime DEFAULT NULL,
						  `orderdata` text,
						  `formdata` text,
						  `model` varchar(45) DEFAULT NULL,
						  `sent` int(11) NOT NULL DEFAULT \'0\',
						  `envelopeid` varchar(255) DEFAULT NULL,
						  PRIMARY KEY (`idshipments`),
						  KEY `FK_shipments_orderid` (`orderid`),
						  CONSTRAINT `FK_shipments_orderid` FOREIGN KEY (`orderid`) REFERENCES `order` (`idorder`) ON DELETE CASCADE ON UPDATE NO ACTION
						) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8', array());
	}

	public function down (){
	}

}