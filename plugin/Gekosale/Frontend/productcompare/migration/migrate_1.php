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
namespace Gekosale\Frontend\Productcompare;

use Gekosale\Db;
use Gekosale\Helper;
use Gekosale\Session;

class Migrate_1 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql("
            CREATE TABLE IF NOT EXISTS `productcompare` (
                `idproductcompare` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `productid` int(10) unsigned NOT NULL,
                `clientid` int(10) unsigned DEFAULT NULL,
                `viewid` int(10) unsigned DEFAULT NULL,
                PRIMARY KEY (`idproductcompare`),
                UNIQUE KEY `product_client` (`productid`,`clientid`,`viewid`),
                KEY `FK_idproductcompare_productid` (`productid`),
                FOREIGN KEY (`clientid`) REFERENCES `client` (`idclient`) ON DELETE CASCADE ON UPDATE NO ACTION,
                KEY `FK_idproductcompare_clientid` (`clientid`),
                FOREIGN KEY (`productid`) REFERENCES `product` (`idproduct`) ON DELETE CASCADE ON UPDATE NO ACTION,
                KEY `FK_idproductcompare_viewid` (`viewid`),
                FOREIGN KEY (`viewid`) REFERENCES `view` (`idview`) ON DELETE CASCADE ON UPDATE NO ACTION
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
        ", array());
		
		$this->uninstallController('productcompare', 1);
		
		$this->installController('productcompare', 'Porównywarka produktów', 0);
		
		$this->execSql("INSERT INTO `controllerseo` VALUES (
                            NULL,
                            'porownywarka',
                            '1',
                            CURRENT_TIMESTAMP,
                            (SELECT `idcontroller` FROM `controller` WHERE `name` = 'productcompare')
                        )", array());
		
		$this->execSql("INSERT INTO `subpage` VALUES (NULL, 'ProductCompare', 'Porównywarka produktów', 'TXT_PRODUCT_COMPARE');", array());
		
		$subpageId = Db::getInstance()->lastInsertId();
		
		$viewId = (Helper::getViewId() > 0) ? Helper::getViewId() : App::getRegistry()->loader->getLayerViewId();
		
		$result = $this->execSql("SELECT `pageschemeid` FROM `view` WHERE `idview` = :idview", array(
			'idview' => $viewId
		));
		
		$pageschemeId = reset($result->fetch());
		
		$this->execSql("INSERT INTO `subpagelayout` VALUES (NULL, :subpageid, :pageschemeid);", array(
			'subpageid' => $subpageId,
			'pageschemeid' => $pageschemeId
		));
		
		$subpageLayoutId = Db::getInstance()->lastInsertId();
		
		$this->execSql("INSERT INTO `subpagelayoutcolumn` VALUES (NULL, :subpagelayoutid, '1', '980', CURRENT_TIMESTAMP, :viewid)", array(
			'subpagelayoutid' => $subpageLayoutId,
			'viewid' => $viewId
		));
		
		$this->execSql("INSERT INTO `layoutbox` VALUES (NULL, 'Porównywarka produktów', CURRENT_TIMESTAMP, :pageschemeid, 'Porównywarka', NULL, 'ProductCompareBox');", array(
			'pageschemeid' => $pageschemeId
		));
	}

	public function down ()
	{
	}
}