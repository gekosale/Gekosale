<?php

namespace SimpleForm\Rules;

class Format extends \SimpleForm\Rule
{
	protected $_format;

	public function __construct ($errorMsg, $format)
	{
		parent::__construct($errorMsg);
		$this->_format = $format;
	}

	public function _Check ($value)
	{
		if (strlen($value) == 0){
			return true;
		}
		return (preg_match($this->_format, $value) == 1);
	}

}
