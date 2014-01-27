<?php

namespace FormEngine\Rules;

class Compare extends \FormEngine\Rule
{

    protected $_compareWith;

    public function __construct ($errorMsg, \FormEngine\Elements\Field $compareWith)
    {
        parent::__construct($errorMsg);
        $this->_compareWith = $compareWith;
    }

    protected function _Check ($value)
    {
        return ($value == $this->_compareWith->GetValue());
    }

    public function Render ()
    {
        $errorMsg = addslashes($this->_errorMsg);
        $field = addslashes($this->_compareWith->getName());
        return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}', sFieldToCompare: '{$field}'}";
    }
}
