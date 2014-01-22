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
namespace Gekosale\Admin\Coupons;

class Migrate_2 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql("
			CREATE TABLE `couponsproduct` (
				`idcouponproduct` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`productid` INT(10) UNSIGNED NOT NULL,
				`couponid` INT(10) UNSIGNED NOT NULL,
				PRIMARY KEY (`idcouponproduct`) USING BTREE,
				INDEX `FK_couponsproduct_categoryid` (`productid`) USING BTREE,
				INDEX `FK_couponsproduct_couponid` (`couponid`),
				CONSTRAINT `FK_couponsproduct_productid` FOREIGN KEY (`productid`) REFERENCES `product` (`idproduct`) ON UPDATE NO ACTION ON DELETE CASCADE,
				CONSTRAINT `FK_couponsproduct_couponid` FOREIGN KEY (`couponid`) REFERENCES `coupons` (`idcoupons`) ON UPDATE NO ACTION ON DELETE CASCADE
			)
			COLLATE='utf8_general_ci'
			ENGINE=InnoDB;
		", array());
	}

	public function down ()
	{
	}
}