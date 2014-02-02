<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @category    Gekosale 
 * @package     Gekosale\Core\Form 
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Form;

abstract class Rule
{

    protected $_errorMsg;

    public function __construct ($errorMsg)
    {
        $this->_errorMsg = $errorMsg;
    }

    public function Check ($value)
    {
        if ($this->_Check($value) === true) {
            return true;
        }
        return $this->GetFailureMessage();
    }

    abstract protected function _Check ($value);

    public function GetType ()
    {
        $classPath = explode('\\', get_class($this));
        return strtolower(end($classPath));
    }

    public function GetFailureMessage ()
    {
        return $this->_errorMsg;
    }

    public function Render ()
    {
        $errorMsg = addslashes($this->_errorMsg);
        return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}'}";
    }
}
