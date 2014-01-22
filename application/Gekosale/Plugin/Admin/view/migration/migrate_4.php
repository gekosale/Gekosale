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
namespace Gekosale\Admin\View;

class Migrate_4 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('ALTER TABLE `view` ADD COLUMN `defaultvatid` INT UNSIGNED NULL DEFAULT NULL  AFTER `ceneoguid`, 
						  ADD CONSTRAINT `FK_view_defaultvatid`
						  FOREIGN KEY (`defaultvatid` )
						  REFERENCES `vat` (`idvat` )
						  ON DELETE SET NULL
						  ON UPDATE NO ACTION
						, ADD INDEX `FK_view_defaultvatid` (`defaultvatid` ASC)', array());
		
		$this->execSql('UPDATE `view` SET defaultvatid = (SELECT idvat FROM vat ORDER BY value DESC LIMIT 1)', array());
	}

	public function down ()
	{
	}
} 