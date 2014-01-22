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

class Migrate_3 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('DELETE FROM `orderproductcode` WHERE orderid NOT IN (SELECT idorder FROM `order`)', array());
		$this->execSql('ALTER TABLE `orderproductcode` CHANGE COLUMN `orderproductid` `orderproductid` INT(11) UNSIGNED NOT NULL  , 
						  ADD CONSTRAINT `FK_orderproductcode_orderid`
						  FOREIGN KEY (`orderproductid` )
						  REFERENCES `order` (`idorder` )
						  ON DELETE CASCADE
						  ON UPDATE NO ACTION, 
						  ADD CONSTRAINT `FK_orderproductcode_orderproductid`
						  FOREIGN KEY (`orderproductid` )
						  REFERENCES `orderproduct` (`idorderproduct` )
						  ON DELETE CASCADE
						  ON UPDATE NO ACTION
						, ADD INDEX `FK_orderproductcode_orderid` (`orderproductid` ASC) 
						, ADD INDEX `FK_orderproductcode_orderproductid` (`orderproductid` ASC)', array());
	}

	public function down ()
	{
	}
} 