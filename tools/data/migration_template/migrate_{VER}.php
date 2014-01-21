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
namespace {PLUGIN_NS}\{PLUGIN_MODE}\{PLUGIN_NAME};


class Migrate_{VER} extends \Gekosale\Component\Migration
{
	
	public function up(){
		$this->installController('{PLUGIN_NAME}', 'Opis kontrolera');
	}
	
	public function down(){
		$this->uninstallController('{PLUGIN_NAME}');
	}
	
} 