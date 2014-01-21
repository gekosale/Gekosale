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

namespace FormEngine\Conditions;

class ConditionOr extends \FormEngine\Condition
{

	public function Evaluate ($value)
	{
		if (is_subclass_of($this->_argument, 'FormEngine\Condition')){
			return $this->_argument->Evaluate($value);
		}
		if (is_array($this->_argument)){
			foreach ($this->_argument as $part){
				if (! is_subclass_of($part, 'FormEngine\Condition')){
					return false;
				}
				if ($part->Evaluate($value)){
					return true;
				}
			}
		}
		return false;
	}

}
