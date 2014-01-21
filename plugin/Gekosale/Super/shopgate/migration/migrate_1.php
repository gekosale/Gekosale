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
namespace Gekosale\Super\Shopgate;

class migrate_1 extends \Gekosale\Component\Migration
{

    public function up ()
    {
        $this->execSql('INSERT INTO event SET name = \'admin.view.initForm\', model = \'shopgate\', method = \'addFields\', module = \'Gekosale_Shopgate\'', array());
        $this->execSql('INSERT INTO event SET name = \'admin.view.model.save\', model = \'shopgate\', method = \'saveSettings\', module = \'Gekosale_Shopgate\'', array());
    }

    public function down ()
    {
    }
}