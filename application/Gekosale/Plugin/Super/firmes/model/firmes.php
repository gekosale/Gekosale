<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2011 Gekosale. Zabronione jest usuwanie informacji o
 * licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 279 $
 * $Author: gekosale $
 * $Date: 2011-07-28 23:13:43 +0200 (Cz, 28 lip 2011) $
 * $Id: product.php 279 2011-07-28 21:13:43Z gekosale $
 */
namespace Gekosale\Plugin;

class FirmesModel extends Component\Model
{

	public function testConnection ()
	{
		return Array(
			'connected' => 1
		);
	}

	public function clearSerialization ($dir, $DeleteMe)
	{
		if (! $dh = @opendir($dir))
			return;
		while (false !== ($obj = readdir($dh))){
			if ($obj == '.' || $obj == '..')
				continue;
			if (! @unlink($dir . '/' . $obj))
				$this->clearSerialization($dir . '/' . $obj, true);
		}
		
		closedir($dh);
		if ($DeleteMe){
			@rmdir($dir);
		}
	}

	public function clearCache ($request)
	{
		$this->clearSerialization(ROOTPATH . DS . 'serialization', false);
		
		$this->updateProductAttributesetPricesAll();
		
		$this->refreshCategories();
		
		return Array(
			'clear' => 1
		);
	}

	public function refreshCategories ()
	{
		Db::getInstance()->beginTransaction();
		
		$sql = 'SELECT idcategory, firmesid FROM category WHERE firmesid > 0';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$sql2 = 'UPDATE category SET categoryid = :idcategory WHERE firmesparentid = :firmesid';
			$stmt2 = Db::getInstance()->prepare($sql2);
			$stmt2->bindValue('idcategory', $rs['idcategory']);
			$stmt2->bindValue('firmesid', $rs['firmesid']);
			$stmt2->execute();
		}
		
		Db::getInstance()->commit();
	}

	public function autoGenerateSymbols ()
	{
		$sql = "UPDATE product SET ean = idproduct WHERE ean = ''";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		
		$sql = "UPDATE productattributeset, product SET
					productattributeset.symbol = IF(product.ean != '', CONCAT(product.ean,'_', productattributeset.idproductattributeset), CONCAT(product.idproduct, '_', productattributeset.idproductattributeset))
				WHERE productattributeset.productid = product.idproduct AND productattributeset.symbol = ''";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
	}

	public function updateProductAttributesetPricesAll ()
	{
		$sql = 'UPDATE productattributeset, product SET
					productattributeset.attributeprice =
					CASE
						WHEN (productattributeset.suffixtypeid = 1) THEN ROUND(product.sellprice * (productattributeset.value / 100), 4)
						WHEN (productattributeset.suffixtypeid = 2) THEN ROUND(product.sellprice + productattributeset.value,4)
						WHEN (productattributeset.suffixtypeid = 3) THEN ROUND(product.sellprice - productattributeset.value,4)
						WHEN (productattributeset.suffixtypeid = 4) THEN ROUND(productattributeset.value,4)
					END,
					productattributeset.discountprice =
					IF(product.promotion = 1,
					CASE
						WHEN (productattributeset.suffixtypeid = 1) THEN ROUND(product.discountprice * (productattributeset.value / 100), 4)
						WHEN (productattributeset.suffixtypeid = 2) THEN ROUND(product.discountprice + productattributeset.value,4)
						WHEN (productattributeset.suffixtypeid = 3) THEN ROUND(product.discountprice - productattributeset.value,4)
						WHEN (productattributeset.suffixtypeid = 4) THEN ROUND(productattributeset.value,4)
					END, NULL)
				WHERE productattributeset.productid = product.idproduct';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
	}
}