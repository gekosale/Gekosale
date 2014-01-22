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
namespace Gekosale\Admin\Availablity;

class Migrate_2 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('ALTER TABLE `product` 
						CHANGE COLUMN `availablityid` `availablityid` INT(11) UNSIGNED NULL DEFAULT NULL  , 
						ADD CONSTRAINT `FK_product_availablityid`
						FOREIGN KEY (`availablityid` )
						REFERENCES `availablity` (`idavailablity` )
						ON DELETE SET NULL
						ON UPDATE NO ACTION
						, ADD INDEX `FK_product_availablityid` (`availablityid` ASC)', array());
		$this->execSql('ALTER TABLE `availablitytranslation` DROP FOREIGN KEY `FK_availablitytranslation_availablityid` , DROP FOREIGN KEY `FK_availablitytranslation_languageid`', array());
		$this->execSql('ALTER TABLE `availablitytranslation` 
						ADD CONSTRAINT `FK_availablitytranslation_availablityid` FOREIGN KEY (`availablityid` ) REFERENCES `availablity` (`idavailablity` ) ON DELETE CASCADE ON UPDATE NO ACTION, 
  						ADD CONSTRAINT `FK_availablitytranslation_languageid` FOREIGN KEY (`languageid` ) REFERENCES `language` (`idlanguage` ) ON DELETE CASCADE ON UPDATE NO ACTION', array());
	}

	public function down ()
	{
	}
} 