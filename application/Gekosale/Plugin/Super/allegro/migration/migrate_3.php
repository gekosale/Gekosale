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

class Migrate_3 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('CREATE  TABLE `allegrorelatedcategories` (
						  `idallegrorelatedcategories` INT NOT NULL AUTO_INCREMENT ,
						  `allegrocategoriesid` INT UNSIGNED NOT NULL ,
						  `categoryid` INT UNSIGNED NOT NULL ,
						  PRIMARY KEY (`idallegrorelatedcategories`) ,
						  INDEX `FK_allegrorelatedcategories_categoryid` (`categoryid` ASC) ,
						  CONSTRAINT `FK_allegrorelatedcategories_categoryid`
						    FOREIGN KEY (`categoryid` )
						    REFERENCES `category` (`idcategory` )
						    ON DELETE CASCADE
						    ON UPDATE NO ACTION)', array());
		
		$this->execSql('CREATE  TABLE `allegrofavouritecategory` (
		  `idallegrofavouritecategory` INT NOT NULL AUTO_INCREMENT ,
		  `allegrooriginalcategoryid` INT UNSIGNED NOT NULL ,
		  `countryid` INT UNSIGNED NOT NULL ,
		  PRIMARY KEY (`idallegrofavouritecategory`) )', array());
	}

	public function down ()
	{
	}
}