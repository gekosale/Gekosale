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

class Migrate_14 extends \Gekosale\Component\Migration
{

    public function up ()
    {
        $this->execSql('INSERT INTO event SET name = \'admin.view.initForm\', model = \'shipment/elektronicznynadawca\', method = \'addFields\', module = \'Gekosale_ElektronicznyNadawca\'', array());
        $this->execSql('INSERT INTO event SET name = \'admin.view.model.save\', model = \'shipment/elektronicznynadawca\', method = \'saveSettings\', module = \'Gekosale_ElektronicznyNadawca\'', array());
        $this->execSql('INSERT INTO event SET name = \'admin.view.initForm\', model = \'shipment/dpd\', method = \'addFields\', module = \'Gekosale_Dpd\'', array());
        $this->execSql('INSERT INTO event SET name = \'admin.view.model.save\', model = \'shipment/dpd\', method = \'saveSettings\', module = \'Gekosale_Dpd\'', array());
        $this->execSql('INSERT INTO event SET name = \'admin.view.initForm\', model = \'shipment/furgonetka\', method = \'addFields\', module = \'Gekosale_Furgonetka\'', array());
        $this->execSql('INSERT INTO event SET name = \'admin.view.model.save\', model = \'shipment/furgonetka\', method = \'saveSettings\', module = \'Gekosale_Furgonetka\'', array());
    }

    public function down ()
    {
    }
}