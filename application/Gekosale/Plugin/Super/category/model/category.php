<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o
 * licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 602 $
 * $Author: gekosale $
 * $Date: 2011-11-07 22:45:33 +0100 (Pn, 07 lis 2011) $
 * $Id: category.php 602 2011-11-07 21:45:33Z gekosale $
 */
namespace Gekosale\Plugin;

use xajaxResponse;

class categoryModel extends Component\Model
{
	public function getCategoriesPathById ()
	{
		Db::getInstance()->beginTransaction();

		$sql = 'TRUNCATE categorypath';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();

		$sql = 'SELECT idcategory AS id, categoryid AS parent FROM category';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$Data = $stmt->fetchAll();
		$parents = Array();
		foreach ($Data as $category){
			if ($category['parent']){
				$parents[$category['id']] = $category['parent'];
			}
			else{
				$parents[$category['id']] = null;
			}
		}
		$alreadyAdded = Array();
		foreach ($parents as $category => $ancestor){
			$order = 0;
			$ancestor = $category;
			for ($i = 0; $i < 50; $i ++){
				if (! isset($alreadyAdded[$category]) || ! isset($alreadyAdded[$category][$ancestor]) || ! $alreadyAdded[$category][$ancestor]){
					$sql = '
							INSERT INTO categorypath
							SET
								categoryid = :categoryid,
								ancestorcategoryid = :ancestorcategoryid,
								`order` = :order
						';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('categoryid', $category);
					$stmt->bindValue('ancestorcategoryid', $ancestor);
					$stmt->bindValue('order', $order ++);
					$stmt->execute();
					$alreadyAdded[$category][$ancestor] = true;
				}
				if ($parents[$ancestor] == null){
					break;
				}
				$ancestor = $parents[$ancestor];
			}
		}

		Db::getInstance()->commit();
		$this->flushCache();
	}

	public function flushCache ()
	{
		App::getContainer()->get('cache')->delete('categories');
		App::getContainer()->get('cache')->delete('sitemapcategories');
	}
}