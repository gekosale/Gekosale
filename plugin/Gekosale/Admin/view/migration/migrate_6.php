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

class Migrate_6 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('ALTER TABLE `view` ADD COLUMN `enablegiftwrap` INT NOT NULL DEFAULT 0  AFTER `defaultvatid` , ADD COLUMN `giftwrapproduct` INT UNSIGNED NULL DEFAULT NULL  AFTER `enablegiftwrap` , 
						  ADD CONSTRAINT `FK_view_giftwrapproduct`
						  FOREIGN KEY (`giftwrapproduct` )
						  REFERENCES `product` (`idproduct` )
						  ON DELETE SET NULL
						  ON UPDATE NO ACTION
						, ADD INDEX `FK_view_giftwrapproduct` (`giftwrapproduct` ASC)', array());
	}

	public function down ()
	{
	}
} 