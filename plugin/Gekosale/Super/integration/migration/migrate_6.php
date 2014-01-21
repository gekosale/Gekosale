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
namespace Gekosale\Super\Integration;

class Migrate_6 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('INSERT INTO event SET name = \'admin.view.initForm\', model = \'integration/ceneo\', method = \'addFieldsView\', module = \'Gekosale_Ceneo\'', array());
		$this->execSql('INSERT INTO event SET name = \'admin.view.model.save\', model = \'integration/ceneo\', method = \'integrationUpdateView\', module = \'Gekosale_Ceneo\'', array());
		$sql = 'SELECT idview, ceneoguid FROM view';
		$stmt = \Gekosale\Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			if (strlen($rs['ceneoguid']) > 0){
				$Data = Array(
					'ceneoguid' => $rs['ceneoguid']
				);
				\Gekosale\App::getRegistry()->core->saveModuleSettings('ceneo', $Data, $rs['idview']);
			}
		}
		
		$this->execSql('ALTER TABLE `view` DROP COLUMN `ceneoguid`', array());
	}

	public function down ()
	{
	}
} 