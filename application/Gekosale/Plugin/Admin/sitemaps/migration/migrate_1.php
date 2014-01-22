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
namespace Gekosale\Admin\Sitemaps;

class Migrate_1 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql("ALTER TABLE `sitemaps` ADD COLUMN `changefreqforcategories` VARCHAR(45) NOT NULL DEFAULT 'always'  AFTER `pingserver` , ADD COLUMN `changefreqforproducts` VARCHAR(45) NOT NULL DEFAULT 'always'  AFTER `changefreqforcategories` , ADD COLUMN `changefreqforproducers` VARCHAR(45) NOT NULL DEFAULT 'always'  AFTER `changefreqforproducts` , ADD COLUMN `changefreqfornews` VARCHAR(45) NOT NULL DEFAULT 'always'  AFTER `changefreqforproducers` , ADD COLUMN `changefreqforpages` VARCHAR(45) NOT NULL DEFAULT 'always'  AFTER `changefreqfornews`", array());
	}

	public function down ()
	{
	}
}