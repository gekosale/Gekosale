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
namespace Gekosale\Super\Allegro;

class Migrate_4 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('CREATE  TABLE `allegrocategories` (
					  `idallegrocategories` INT NOT NULL AUTO_INCREMENT ,
					  `name` VARCHAR(255) NOT NULL ,
					  `countryid` INT UNSIGNED NOT NULL ,
					  `parentcategoryid` INT UNSIGNED NOT NULL ,
					  `originalcategoryid` INT UNSIGNED NOT NULL ,
					  `categoryposition` INT UNSIGNED NOT NULL ,
					  PRIMARY KEY (`idallegrocategories`) )', array());
	}

	public function down ()
	{
	}
}