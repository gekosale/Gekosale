<?php

namespace SimpleForm\Rules;

class RequiredDependency extends \SimpleForm\Rule
{
	protected $_field;
	protected $_condition;

	public function __construct ($errorMsg, $field, $condition)
	{
		parent::__construct($errorMsg);
		$this->_field = $field;
		$this->_condition = $condition;
	}

	public function FormatDependencyJS ()
	{
		switch ($this->_condition->GetType()) {
			case \SimpleForm\Condition::EQUALS:
				if ($this->_field instanceof \SimpleForm\Elements\Radio){
					return "'#{$this->_field->GetId()}_{$this->_condition->GetArgument()}:checked";
				}
				if ($this->_field instanceof \SimpleForm\Elements\Checkbox){
					return "'#{$this->_field->GetId()}:checked";
				}
				
				break;
		}
	}

	public function _Check ($value)
	{
		if ($this->_field->GetValue() == $this->_condition->GetArgument()){
			if (strlen($value) > 0){
				return true;
			}
			else{
				return false;
			}
		}
		return true;
	}
}
