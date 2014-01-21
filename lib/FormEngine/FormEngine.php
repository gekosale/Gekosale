<?php defined('ROOTPATH') OR die('No direct access allowed.');
/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms 
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the 
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */

class FE {
	
	const INFINITE = 'inf';
	
	const TYPE_NUMBER = 'number';
	const TYPE_STRING = 'string';
	const TYPE_FUNCTION = 'function';
	const TYPE_ARRAY = 'array';
	const TYPE_OBJECT = 'object';
	const TYPE_BOOLEAN = 'boolean';
	
	public static function SubmittedData() {
		return $_POST;
	}
	
	public function IsAction($actionName) {
		$actionName = '_Action_' . $actionName;
		return (isset($_POST[$actionName]) and ($_POST[$actionName] == '1'));
	}
	
}
