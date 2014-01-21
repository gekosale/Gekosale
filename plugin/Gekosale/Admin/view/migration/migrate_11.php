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
namespace Gekosale\Admin\View;

class Migrate_11 extends \Gekosale\Component\Migration
{

    public function up ()
    {
        $this->execSql('ALTER TABLE `view` ADD COLUMN `orderstatusgroupsid` INT UNSIGNED NULL DEFAULT NULL  AFTER `sendingo` , 
						  ADD CONSTRAINT `FK_view_orderstatusgroupsid`
						  FOREIGN KEY (`orderstatusgroupsid` )
						  REFERENCES `orderstatusgroups` (`idorderstatusgroups` )
						  ON DELETE SET NULL
						  ON UPDATE NO ACTION
						, ADD INDEX `FK_view_orderstatusgroupsid` (`orderstatusgroupsid` ASC)', array());
        
        $sql = 'SELECT idorderstatusgroups FROM orderstatusgroups';
        $stmt = \Gekosale\Db::getInstance()->prepare($sql);
        $stmt->execute();
        while ($rs = $stmt->fetch()){
            $this->updateViewOrderStatusGroup($rs['idorderstatusgroups']);
        }
    }

    public function updateViewOrderStatusGroup ($id)
    {
        $sql = 'UPDATE `view` SET orderstatusgroupsid = :orderstatusgroupsid';
        $stmt = \Gekosale\Db::getInstance()->prepare($sql);
        $stmt->bindValue('orderstatusgroupsid', $id);
        $stmt->execute();
    }

    public function down ()
    {
    }
}