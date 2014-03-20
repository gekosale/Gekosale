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

/**
 * Class Rule
 *
 * @package Gekosale\Core\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
abstract class Rule
{

    protected $_errorMsg;

    public function __construct($errorMsg)
    {
        $this->_errorMsg = $errorMsg;
    }

    public function check($value)
    {
        if ($this->checkValue($value) === true) {
            return true;
        }

        return $this->getFailureMessage();
    }

    abstract protected function checkValue($value);

    public function getType()
    {
        $class = explode('\\', get_class($this));

        return strtolower(end($class));
    }

    public function getFailureMessage()
    {
        return $this->_errorMsg;
    }

    public function render()
    {
        $errorMsg = addslashes($this->_errorMsg);

        return "{sType: '{$this->getType()}', sErrorMessage: '{$errorMsg}'}";
    }
}
