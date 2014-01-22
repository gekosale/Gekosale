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
 * 
 * 
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: categoriesbox.php 438 2011-08-27 09:29:36Z gekosale $
 */

namespace Gekosale\Plugin;

class MainCategoriesBoxModel extends Component\Model
{

	public function getMainCategories ()
	{
		$sql = "SELECT 
					C.idcategory, 
					CT.name,
					CT.seo,
					C.photoid,
					CT.shortdescription,
					CT.description,
     				COUNT(PC.productid) AS totalproducts,
     				MIN(P.sellprice) AS minsellprice
				FROM category C
				LEFT JOIN viewcategory VC ON VC.categoryid = C.idcategory
				LEFT JOIN categorypath CP ON CP.ancestorcategoryid = C.idcategory
				LEFT JOIN productcategory PC ON CP.categoryid = PC.categoryid
				LEFT JOIN product P ON PC.productid = P.idproduct
				LEFT JOIN categorytranslation CT ON CT.categoryid = idcategory AND CT.languageid = :languageid
				WHERE C.categoryid IS NULL AND VC.viewid=:viewid AND C.enable = 1
				GROUP BY C.idcategory";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'name' => $rs['name'],
				'idcategory' => $rs['idcategory'],
				'qry' => $rs['totalproducts'],
				'seo' => $rs['seo'],
				'minsellprice' => $this->registry->core->processPrice($rs['minsellprice']),
				'shortdescription' => $rs['shortdescription'],
				'description' => $rs['description'],
				'photo' => $this->getImagePath($rs['photoid'])
			);
		}
		return $Data;
	}

	public function getImagePath ($id)
	{
		if ($id > 0){
			return App::getModel('gallery')->getImagePath(App::getModel('gallery')->getSmallImageById($id));
		}
	}

}