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
namespace Gekosale\Admin\Collection;

class Migrate_1 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql('CREATE TABLE `collection` (
  						`idcollection` int(10) unsigned NOT NULL AUTO_INCREMENT,
						`adddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
						`photoid` int(10) unsigned DEFAULT NULL,
						`migrationid` int(11) DEFAULT NULL,
  						PRIMARY KEY (`idcollection`),
  						KEY `FK_collection_fileid` (`photoid`),
  						CONSTRAINT `FK_collection_photoid` FOREIGN KEY (`photoid`) REFERENCES `file` (`idfile`) ON DELETE SET NULL ON UPDATE NO ACTION
						) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8', array());
		
		$this->execSql('CREATE TABLE `collectiontranslation` (
  						`idcollectiontranslation` int(10) unsigned NOT NULL AUTO_INCREMENT,
  						`name` varchar(255) NOT NULL,
  						`collectionid` int(10) unsigned NOT NULL,
  						`languageid` int(10) unsigned NOT NULL,
  						`seo` varchar(255) DEFAULT NULL,
  						`description` text,
  						`keyword_title` varchar(255) DEFAULT NULL,
  						`keyword` varchar(255) DEFAULT NULL,
  						`keyword_description` varchar(255) DEFAULT NULL,
  						PRIMARY KEY (`idcollectiontranslation`),
  						UNIQUE KEY `UNIQUE_collectiontranslation_name_languageid` (`name`,`languageid`),
  						KEY `FK_collectiontranslation_collectionid` (`collectionid`),
  						KEY `FK_collectiontranslation_languageid` (`languageid`),
  						CONSTRAINT `FK_collectiontranslation_languageid` FOREIGN KEY (`languageid`) REFERENCES `language` (`idlanguage`) ON DELETE CASCADE ON UPDATE NO ACTION,
  						CONSTRAINT `FK_collectiontranslation_collectionid` FOREIGN KEY (`collectionid`) REFERENCES `collection` (`idcollection`) ON DELETE CASCADE ON UPDATE NO ACTION
						) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8', array());
		
		$this->execSql('CREATE TABLE `collectionview` (
						`idcollectionview` int(10) unsigned NOT NULL AUTO_INCREMENT,
						`collectionid` int(10) unsigned NOT NULL,
						`viewid` int(10) unsigned NOT NULL,
						PRIMARY KEY (`idcollectionview`),
						KEY `FK_collectionview_collectionid` (`collectionid`),
						KEY `FK_collectionview_viewid` (`viewid`),
						CONSTRAINT `FK_collectionview_collectionid` FOREIGN KEY (`collectionid`) REFERENCES `collection` (`idcollection`) ON DELETE CASCADE ON UPDATE NO ACTION,
						CONSTRAINT `FK_collectionview_viewid` FOREIGN KEY (`viewid`) REFERENCES `view` (`idview`) ON DELETE CASCADE ON UPDATE NO ACTION
						) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8', array());
	}

	public function down ()
	{
	}
} 