<?php
namespace FormEngine\Rules;
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

class Format extends \FormEngine\Rule
{
	
	protected $_format;

	public function __construct ($errorMsg, $format)
	{
		parent::__construct($errorMsg);
		$this->_format = $format;
	}

	protected function _Check ($value)
	{
		if (strlen($value) == 0){
			return true;
		}
		return (preg_match($this->_format, $value) == 1);
	}

	public function Render ()
	{
		$format = addslashes($this->_format);
		$errorMsg = addslashes($this->_errorMsg);
		return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}', sFormat: '{$format}'}";
	}

}
