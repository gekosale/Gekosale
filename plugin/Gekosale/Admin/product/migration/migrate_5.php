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
namespace Gekosale\Admin\Product;

class Migrate_5 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('ALTER TABLE `productattributeset` ADD COLUMN `availablityid` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `status` , ADD COLUMN `photoid` INT(11) UNSIGNED NULL DEFAULT NULL  AFTER `availablityid` , 
						  ADD CONSTRAINT `FK_productattributeset_availablityid`
						  FOREIGN KEY (`availablityid` )
						  REFERENCES `availablity` (`idavailablity` )
						  ON DELETE SET NULL
						  ON UPDATE NO ACTION, 
						  ADD CONSTRAINT `FK_productattributeset_photoid`
						  FOREIGN KEY (`photoid` )
						  REFERENCES `file` (`idfile` )
						  ON DELETE SET NULL
						  ON UPDATE NO ACTION
						, ADD INDEX `FK_productattributeset_availablityid` (`availablityid` ASC) 
						, ADD INDEX `FK_productattributeset_photoid` (`photoid` ASC)', array());
	}

	public function down ()
	{
	}
} 