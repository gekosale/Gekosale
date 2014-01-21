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
namespace Gekosale\Admin\Sendit;

class Migrate_1 extends \Gekosale\Component\Migration
{

	public function up ()
	{
/*
		$this->execSql('INSERT INTO event SET name = \'admin.view.initForm\', model = \'sendit\', method = \'addFields\', module = \'Gekosale_Sendit\'', array());
		$this->execSql('INSERT INTO event SET name = \'admin.view.model.save\', model = \'sendit\', method = \'saveSettings\', module = \'Gekosale_Sendit\'', array());
		$this->execSql('INSERT INTO controller SET name = \'sendit\', version = 1, description = \'Sendit.pl\', enable = 1, mode = 1', array());
		$this->execSql('INSERT INTO `right` SET controllerid = (SELECT idcontroller FROM controller WHERE name = \'sendit\'), groupid = 1, permission = 127', array());
		$this->execSql('
			CREATE TABLE IF NOT EXISTS `sendit_orders` (
			    `id` int(10) NOT NULL AUTO_INCREMENT,
			    `order_id` int(10) NOT NULL,
			    `order_nr` varchar(30) COLLATE utf8_bin NOT NULL,
			    `status` varchar(255) COLLATE utf8_bin DEFAULT "Zlecenie zlozone",
			    `status_nr` int(1) DEFAULT NULL,
			    `tracking_code` text,
			    `protocol_number` varchar(40) COLLATE utf8_bin DEFAULT NULL,
			    `cod` varchar(11) COLLATE utf8_bin DEFAULT "0",
			    `brutto` varchar(11) COLLATE utf8_bin DEFAULT "0",
			    `courier` varchar(45) COLLATE utf8_bin NULL,
			    PRIMARY KEY (`id`) )
			    ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
		', array());
*/
	}

	public function down ()
	{
	}
}