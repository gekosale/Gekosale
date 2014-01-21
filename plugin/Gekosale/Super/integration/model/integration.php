<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms 
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the 
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace Gekosale;

use FormEngine;

class IntegrationModel extends Component\Model
{

	public function getIntegrationWhitelist ($module)
	{
		$sql = "SELECT 
					* 
				FROM integrationwhitelist IW
				LEFT JOIN integration I ON IW.integrationid = I.idintegration
				WHERE I.symbol = :symbol";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('symbol', $module);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['ipaddress'];
		}
		return $Data;
	}
}