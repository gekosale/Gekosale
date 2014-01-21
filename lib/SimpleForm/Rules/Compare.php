<?php

namespace SimpleForm\Rules;

class Compare extends \SimpleForm\Rule
{
	protected $_compareWith;

	public function __construct ($errorMsg, $compareWith)
	{
		parent::__construct($errorMsg);
		$this->_compareWith = $compareWith;
	}

	public function getComparedField ()
	{
		return $this->_compareWith;
	}

	public function _Check ($value)
	{
		return ($value == $this->_compareWith->GetValue());
	}
}
