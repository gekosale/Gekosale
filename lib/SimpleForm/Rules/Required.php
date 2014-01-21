<?php

namespace SimpleForm\Rules;

class Required extends \SimpleForm\Rule
{

	public function _Check ($value)
	{
		if (strlen($value) > 0){
			return true;
		}
		return false;
	}
}