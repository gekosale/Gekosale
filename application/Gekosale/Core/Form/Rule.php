<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace Gekosale\Core\Form;

abstract class Rule
{

    protected $_errorMsg;

    public function __construct($errorMsg)
    {
        $this->_errorMsg = $errorMsg;
    }

    public function Check($value)
    {
        if ($this->_Check($value) === true) {
            return true;
        }
        return $this->GetFailureMessage();
    }

    abstract protected function _Check($value);

    public function GetType()
    {
        $class = explode('\\', get_class($this));
        return strtolower(end($class));
    }

    public function GetFailureMessage()
    {
        return $this->_errorMsg;
    }

    public function Render()
    {
        $errorMsg = addslashes($this->_errorMsg);
        return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}'}";
    }
}
