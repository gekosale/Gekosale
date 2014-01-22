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
 * $Id: controller.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Plugin;

class ControllerModel extends Component\Model
{

	public function getControllerSimpleList ()
	{
		$sql = 'SELECT idcontroller AS id, name, description FROM controller WHERE mode = 1';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $this->trans($rs['description'])
			);
		}
		return $Data;
	}

	public function getControllerSimpleListToSelect ()
	{
		$Data = $this->getControllerSimpleList();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$Data['id']] = $Data['name'];
		}
		return $tmp;
	}
}