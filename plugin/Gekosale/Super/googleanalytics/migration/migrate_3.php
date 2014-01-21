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

namespace Gekosale\Super\Googleanalytics;

class Migrate_3 extends \Gekosale\Component\Migration
{

    public function up ()
	{
		
        $sql = 'SELECT idview, gacode, gauniversal, gapages, gatransactions FROM view';
        $stmt = \Gekosale\Db::getInstance()->prepare($sql);
        $stmt->execute();
        while ($rs = $stmt->fetch()){
           $Settings = Array(
				'gacode' => $rs['gacode'],
				'enableuniversalga' => $rs['gauniversal'],
				'gatransactions' => $rs['gatransactions'],
				'gapages' => $rs['gapages']
			);
			
			\Gekosale\App::getRegistry()->core->saveModuleSettings('googleanalytics', $Settings, $rs['idview']);
        }
	}

    public function down ()
    {
    }
}