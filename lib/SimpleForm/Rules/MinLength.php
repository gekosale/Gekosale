<?php

namespace SimpleForm\Rules;

class MinLength extends \SimpleForm\Rule
{
	protected $_length;

	public function __construct ($errorMsg, $length)
	{
		parent::__construct($errorMsg);
		$this->_length = $length;
	}

	public function getLength ()
	{
		return $this->_length;
	}

	public function _Check ($value)
	{
		if (strlen($value) >= $this->_length){
			return true;
		}
		return false;
	}
}