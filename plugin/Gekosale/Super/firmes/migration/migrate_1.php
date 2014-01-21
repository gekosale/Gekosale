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
namespace Gekosale\Super\Firmes;

class Migrate_1 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('ALTER TABLE `category` ADD  `firmesid` INT( 11 ) NOT NULL', array());
		$this->execSql('ALTER TABLE `category` ADD `firmesparentid` INT( 11 ) NOT NULL', array());
		$this->execSql('ALTER TABLE `dispatchmethod` ADD  `subiektsymbol` VARCHAR( 255 ) NOT NULL', array());
		$this->execSql('ALTER TABLE `order` ADD  `firmesid` INT( 11 ) NOT NULL', array());
		$this->execSql('ALTER TABLE `producer` ADD  `firmesid` INT( 11 ) NOT NULL', array());
		$this->execSql('ALTER TABLE `product` ADD  `firmesid` INT( 11 ) NOT NULL', array());
	}

	public function down ()
	{
	}
} 