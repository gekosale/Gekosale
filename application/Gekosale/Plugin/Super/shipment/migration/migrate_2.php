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


class Migrate_2 extends \Gekosale\Component\Migration
{
	
	public function up(){
		$this->execSql(
                "CREATE TABLE IF NOT EXISTS `shipment` (
                `idshipment` int(11) NOT NULL AUTO_INCREMENT,
                `symbol` varchar(128) NOT NULL,
                `shipmentdate` date NOT NULL,
                `dispatchmethod` varchar(20) NOT NULL,
                `dispatchernumber` varchar(50) NOT NULL,
                `comment` text,
                `contentoriginal` blob NOT NULL,
                `contentcopy` blob NOT NULL,
                `orderid` int(11) NOT NULL,
                `viewid` int(11) DEFAULT NULL,
                `externalid` int(11) DEFAULT NULL,
                `contenttype` varchar(5) DEFAULT 'html',
                PRIMARY KEY (`idshipment`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;", array()
                );
	}
	
	public function down(){
		$this->execSql("DROP TABLE IF EXISTS `shipment`", array());
	}
	
} 