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

class Migrate_2 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('ALTER TABLE `subpagelayoutcolumn` DROP FOREIGN KEY `subpagelayoutcolumn_ibfk_1`', array());
		$this->execSql('ALTER TABLE `subpagelayoutcolumn` 
						  ADD CONSTRAINT `subpagelayoutcolumn_ibfk_1`
						  FOREIGN KEY (`subpagelayoutid` )
						  REFERENCES `subpagelayout` (`idsubpagelayout` )
						  ON DELETE CASCADE
						  ON UPDATE NO ACTION', array());
	}

	public function down ()
	{
	}
} 