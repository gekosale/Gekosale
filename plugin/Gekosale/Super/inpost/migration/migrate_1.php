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
namespace Gekosale\Super\Inpost;

class Migrate_1 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql("INSERT INTO event (`name`, `model`, `method`, `module`) VALUES ('admin.menu.create', 'inpost', 'renderMenu', 'Gekosale_InPost')", array());
		$this->execSql("INSERT INTO event SET name = 'admin.view.initForm', model = 'inpost', method = 'addFields', module = 'Gekosale_InPost'", array());
		$this->execSql("INSERT INTO event SET name = 'frontend.cartbox.renderForm', model = 'inpost', method = 'addFieldsCartbox', module = 'Gekosale_InPost'", array());
		$this->execSql("INSERT INTO event SET name = 'admin.view.model.save', model = 'inpost', method = 'saveSettings', module = 'Gekosale_InPost'", array());
		$this->execSql("INSERT INTO event SET name = 'frontend.order.saveOrder', model = 'inpost', method = 'saveOrder', module = 'Gekosale_InPost'", array());
		$this->execSql("ALTER TABLE `order` ADD COLUMN `paczkomat` VARCHAR(45) NULL", array());
		$this->execSql("ALTER TABLE `order` ADD COLUMN `inpostpackage` VARCHAR(45) NULL", array());
		$this->execSql("ALTER TABLE `order` ADD COLUMN `packagestatus` VARCHAR(45) NOT NULL DEFAULT 'Pending'", array());
		$this->installController('inpost', 'Integracja Inpost');
	}

	public function down ()
	{
	}
}