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
namespace Gekosale\Admin\Codes;

class Migrate_2 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('UPDATE event SET name = \'admin.product.initForm\' WHERE name = \'admin.product.renderForm\' AND model = \'codes\'', array());
		$this->execSql('DELETE FROM event WHERE name = \'admin.product.populateForm\' AND model = \'codes\'', array());
	}

	public function down ()
	{
	}
} 