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
namespace FormEngine;

class PriceFactor
{
	protected $_type;
	protected $_value;
	const TYPE_PERCENTAGE = 'GPriceFactor.TYPE_PERCENTAGE';
	const TYPE_ADD = 'GPriceFactor.TYPE_ADD';
	const TYPE_SUBTRACT = 'GPriceFactor.TYPE_SUBTRACT';
	const TYPE_EQUALS = 'GPriceFactor.TYPE_EQUALS';

	public function __construct ($type, $value)
	{
		$this->_type = $type;
		$this->_value = $value;
	}

	public function Render ()
	{
		return "new GPriceFactor({$this->_type}, {$this->_value})";
	}
}
