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
namespace Gekosale\Super\Shipment;


class Migrate_6 extends \Gekosale\Component\Migration
{
	
	public function up(){
		$this->execSql('ALTER TABLE shipment DROP COLUMN contentoriginal;', array());
		$this->execSql('ALTER TABLE shipment DROP COLUMN contentcopy;', array());

		$this->execSql('ALTER TABLE shipment ADD COLUMN rendered_details BLOB;', array());
		$this->execSql('ALTER TABLE shipment ADD COLUMN rendered_slip BLOB;', array());
        
    }
	
	public function down(){
	}
	
} 