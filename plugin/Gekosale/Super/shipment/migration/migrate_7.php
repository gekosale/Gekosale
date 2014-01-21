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


class Migrate_7 extends \Gekosale\Component\Migration
{
	
	public function up(){
        $this->execSql('ALTER TABLE shipment DROP COLUMN cod_amount;', array());
		$this->execSql('ALTER TABLE shipment DROP COLUMN rendered_details;', array());
		$this->execSql('ALTER TABLE shipment DROP COLUMN rendered_slip;', array());

		$this->execSql('ALTER TABLE shipment ADD COLUMN rendereddetails BLOB;', array());
		$this->execSql('ALTER TABLE shipment ADD COLUMN renderedslip BLOB;', array());
		$this->execSql('ALTER TABLE shipment ADD COLUMN codamount DOUBLE NOT NULL DEFAULT 0;', array());

     }
	
	public function down(){
	}
	
} 