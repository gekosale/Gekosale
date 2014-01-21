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
namespace Gekosale\Admin\Codes;

class Migrate_1 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('INSERT INTO event SET name = \'admin.product.renderForm\', model = \'codes\', method = \'addFields\', module = \'Gekosale_Codes\'', array());
		$this->execSql('INSERT INTO event SET name = \'admin.product.populateForm\', model = \'codes\', method = \'populateFields\', module = \'Gekosale_Codes\'', array());
		$this->execSql('INSERT INTO event SET name = \'admin.product.model.save\', model = \'codes\', method = \'saveSettings\', module = \'Gekosale_Codes\'', array());
		$this->execSql('INSERT INTO controller SET name = \'codes\', version = 1, description = \'Klucze licencyjne\', enable = 1, mode = 1', array());
		$this->execSql('INSERT INTO `right` SET controllerid = (SELECT idcontroller FROM controller WHERE name = \'codes\'), groupid = 1, permission = 127', array());
		$this->execSql('CREATE TABLE IF NOT EXISTS `productcode` (
  					   `idproductcode` int(10) unsigned NOT NULL auto_increment,
  					   `productid` int(11) NOT NULL,
 					   `code` varchar(255) default NULL,
  					   PRIMARY KEY  (`idproductcode`)
					   ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1', array());
		$this->execSql('CREATE TABLE IF NOT EXISTS `orderproductcode` (
					   `idorderproductcode` int(11) NOT NULL auto_increment,
					   `orderid` int(11) NOT NULL,
					   `code` varchar(255) NOT NULL,
					   `orderproductid` int(11) NOT NULL,
					   `ean` varchar(45) default NULL,
					   PRIMARY KEY  (`idorderproductcode`)
					   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1', array());
	}

	public function down ()
	{
		$this->execSql('DELETE FROM event WHERE model = \'codes\'', array());
		$this->execSql('DELETE FROM right WHERE controllerid = (SELECT idcontroller FROM controller WHERE name = \'codes\')', array());
		$this->execSql('DELETE FROM controller WHERE name = \'codes\'', array());
		$this->execSql('DROP TABLE productcode', array());
		$this->execSql('DROP TABLE orderproductcode', array());
	}
} 