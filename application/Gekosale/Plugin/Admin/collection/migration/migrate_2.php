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
namespace Gekosale\Admin\Collection;

class Migrate_2 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('ALTER TABLE `product` ADD COLUMN `collectionid` INT(11) UNSIGNED NULL DEFAULT NULL  AFTER `availablityid`, 
  						ADD CONSTRAINT `FK_product_collectionid`
						FOREIGN KEY (`collectionid` )
						REFERENCES `collection` (`idcollection` )
						ON DELETE CASCADE
						ON UPDATE NO ACTION
						,ADD INDEX `FK_product_collectionid` (`collectionid` ASC)', array());
	}

	public function down ()
	{
	}
} 