<?php

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
namespace SimpleForm;

abstract class Condition
{
	const EQUALS = 'EQUALS';
	protected $_type;
	protected $_argument;

	public function __construct ($argument)
	{
	    $classPath = explode('\\', get_class($this));
		$this->_type = strtoupper(end($classPath));
		$this->_argument = $argument;
	}

	public function GetType ()
	{
		return $this->_type;
	}

	public function GetArgument ()
	{
		return $this->_argument;
	}

	public function Evaluate ($value)
	{
		return true;
	}
}
