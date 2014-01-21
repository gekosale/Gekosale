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

class Migrate_12 extends \Gekosale\Component\Migration
{

	public function up (){
		
		$this->execSql('DELETE FROM `shipment` WHERE orderid NOT IN (SELECT idorder FROM `order`)', array());
		
		$this->execSql('ALTER TABLE `shipment` CHANGE COLUMN `orderid` `orderid` INT(11) UNSIGNED NOT NULL, CHANGE COLUMN `viewid` `viewid` INT(11) UNSIGNED NULL DEFAULT NULL', array());
		
		$this->execSql('ALTER TABLE `shipment` 
						ADD CONSTRAINT `FK_shipment_viewid`	FOREIGN KEY (`viewid` )	REFERENCES `view` (`idview` ) ON DELETE CASCADE	ON UPDATE NO ACTION, 
  						ADD CONSTRAINT `FK_shipment_orderid` FOREIGN KEY (`orderid` ) REFERENCES `order` (`idorder` ) ON DELETE CASCADE	ON UPDATE NO ACTION, 
						ADD INDEX `FK_shipment_viewid` (`viewid` ASC), 
						ADD INDEX `FK_shipment_orderid` (`orderid` ASC)', array());
	}

	public function down (){
	}

}