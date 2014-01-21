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
namespace Gekosale\Admin\Productcombination;

class Migrate_2 extends \Gekosale\Component\Migration
{

	public function up ()
	{
		$this->execSql("
			ALTER TABLE `combinationtranslation` DROP FOREIGN KEY `FK_combination_combinationid` , DROP FOREIGN KEY `FK_combination_languageid` ;
			ALTER TABLE `combinationtranslation` 
			  ADD CONSTRAINT `FK_combination_combinationid`
			  FOREIGN KEY (`combinationid` )
			  REFERENCES `combination` (`idcombination` )
			  ON DELETE CASCADE
			  ON UPDATE NO ACTION, 
			  ADD CONSTRAINT `FK_combination_languageid`
			  FOREIGN KEY (`languageid` )
			  REFERENCES `language` (`idlanguage` )
			  ON DELETE CASCADE
		  ON UPDATE NO ACTION;", array());
		
		$this->execSql('
			ALTER TABLE `combinationview` DROP FOREIGN KEY `FK_combinationview_combinationid` , DROP FOREIGN KEY `FK_combinationview_viewid` ;
			ALTER TABLE `combinationview` 
			  ADD CONSTRAINT `FK_combinationview_combinationid`
			  FOREIGN KEY (`combinationid` )
			  REFERENCES `combination` (`idcombination` )
			  ON DELETE CASCADE
			  ON UPDATE NO ACTION, 
			  ADD CONSTRAINT `FK_combinationview_viewid`
			  FOREIGN KEY (`viewid` )
			  REFERENCES `view` (`idview` )
			  ON DELETE CASCADE
			  ON UPDATE NO ACTION;', array());
		
		$this->execSql('
			ALTER TABLE `productcombination` DROP FOREIGN KEY `FK_productcombination_combinationid` , DROP FOREIGN KEY `FK_productcombination_productattributesetid` , DROP FOREIGN KEY `FK_productcombination_productid` ;
			ALTER TABLE `productcombination` 
			  ADD CONSTRAINT `FK_productcombination_combinationid`
			  FOREIGN KEY (`combinationid` )
			  REFERENCES `combination` (`idcombination` )
			  ON DELETE CASCADE
			  ON UPDATE NO ACTION, 
			  ADD CONSTRAINT `FK_productcombination_productattributesetid`
			  FOREIGN KEY (`productattributesetid` )
			  REFERENCES `productattributeset` (`idproductattributeset` )
			  ON DELETE SET NULL
			  ON UPDATE NO ACTION, 
			  ADD CONSTRAINT `FK_productcombination_productid`
			  FOREIGN KEY (`productid` )
			  REFERENCES `product` (`idproduct` )
			  ON DELETE CASCADE
			  ON UPDATE NO ACTION;', array());
	}

	public function down ()
	{
	}
} 