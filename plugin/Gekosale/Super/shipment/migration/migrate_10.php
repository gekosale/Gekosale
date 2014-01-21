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


class Migrate_10 extends \Gekosale\Component\Migration
{
	
	public function up(){
		$settings = $this->getModuleSettings('shipment');
        
        $settings['superpaczka_base_url'] = 'http://well1-ovh.divante.pl:8001/';
        $settings['superpaczka_hash'] = '5dd041cf8d8350c80180331b08026af00da2e849e946020e0bf75e863d5941c8tel';
        $this->saveModuleSettings('shipment', $settings);
	}
	
	public function down(){
	}
	
} 