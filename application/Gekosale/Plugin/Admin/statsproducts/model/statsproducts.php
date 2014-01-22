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
 * $Id: statsproducts.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale\Plugin;

class StatsproductsModel extends Component\Model
{

	public function getSummaryStats ()
	{
		$Data = Array();
		
		$sql = 'SELECT 
					COUNT(idproduct) AS totalproducts,
					AVG((sellprice - buyprice) / sellprice) * 100 AS averagemargin,
					MAX((sellprice - buyprice) / sellprice) * 100 AS maxmargin,
					MIN((sellprice - buyprice) / sellprice) * 100 AS minmargin
				FROM product';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		if ($rs = $stmt->fetch()){
			$Data['minmargin'] = sprintf('%.2f', $rs['minmargin']);
			$Data['maxmargin'] = sprintf('%.2f', $rs['maxmargin']);
			$Data['averagemargin'] = sprintf('%.2f', $rs['averagemargin']);
			$Data['totalproducts'] = $rs['totalproducts'];
		}
		
		return $Data;
	}
}