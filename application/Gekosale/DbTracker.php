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
 * $Id: db.class.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;

class DbTracker
{

	public static function deleteRows ($table = '', $primaryKey = '', $recordsToDelete = Array())
	{
		if ($table === '' || $primaryKey === '' || empty($recordsToDelete)){
			return false;
		}
		
		try{
			$ids = (is_array($recordsToDelete)) ? $recordsToDelete : (array) $recordsToDelete;
			$sql = 'DELETE FROM `' . $table . '` WHERE ' . $primaryKey . ' IN (' . implode(',', $ids) . ')';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->execute();
			return true;
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
			return false;
		}
		return true;
	}
	
	public static function truncate ($table = '')
	{
		if ($table === ''){
			return false;
		}
		
		try{
			$sql = 'TRUNCATE TABLE `' . $table . '`';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->execute();
			return true;
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
			return false;
		}
		return true;
	}

}