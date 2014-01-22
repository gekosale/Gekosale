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
namespace Gekosale\Admin\SubpageLayout;

class Migrate_1 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('DELETE FROM subpagelayout WHERE pageschemeid NOT IN (SELECT idpagescheme FROM pagescheme)', array());
		$this->execSql('ALTER TABLE `subpagelayout` CHANGE COLUMN `pageschemeid` `pageschemeid` INT(11) UNSIGNED NULL DEFAULT NULL  , 
						  ADD CONSTRAINT `FK_subpagelayout_pageschemeid`
						  FOREIGN KEY (`pageschemeid` )
						  REFERENCES `pagescheme` (`idpagescheme` )
						  ON DELETE CASCADE
						  ON UPDATE NO ACTION
						, ADD INDEX `FK_subpagelayout_pageschemeid` (`pageschemeid` ASC) ;', array());
	}

	public function down ()
	{
	}
} 