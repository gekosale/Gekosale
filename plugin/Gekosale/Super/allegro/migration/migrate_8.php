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

class Migrate_8 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('CREATE  TABLE `allegroorder` (
					  `idallegroorder` INT NOT NULL AUTO_INCREMENT ,
					  `allegropostbuyformid` INT UNSIGNED NOT NULL ,
					  `orderid` INT UNSIGNED NOT NULL ,
					  PRIMARY KEY (`idallegroorder`) ,
					  INDEX `FK_allegroorder_orderid` (`orderid` ASC),
					  CONSTRAINT `FK_allegroorder_orderid`
					    FOREIGN KEY (`orderid` )
					    REFERENCES `order` (`idorder` )
					    ON DELETE CASCADE
					    ON UPDATE NO ACTION)', array());
	}

	public function down ()
	{
	}
}