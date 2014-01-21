<?php

namespace SimpleForm\Rules;

class Custom extends \SimpleForm\Rule
{
	protected $_checkFunction;
	protected $_params;

	public function __construct ($errorMsg, $checkFunctionCallback, $params = array())
	{
		parent::__construct($errorMsg);

		$this->_checkFunction = $checkFunctionCallback;
		$this->_params = $params;
	}

	public function getComparedField ()
	{
		return $this->_compareWith;
	}

	public function _Check ($value)
	{
		return call_user_func($this->_checkFunction, $value, $this->_params);
	}
}
