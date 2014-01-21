<?php
/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the
 * Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it
 * through the
 * world-wide-web, please send an email to license@verison.pl so we can send you
 * a copy immediately.
 */

namespace FormEngine\Filters;
use HTMLPurifier;

require_once ROOTPATH . 'lib' . DS . 'HTMLPurifier' . DS . 'HTMLPurifier.standalone.php';

class Secure extends \FormEngine\Filter
{

	protected function _FilterValue ($value)
	{
// 		if (strlen($value) > 0){
// 			$purifier = new HTMLPurifier();
// 			$clean = $purifier->purify($value);
// 			return $clean;
// 		}
// 		else{
			return $value;
// 		}
	}

}
