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

abstract class Condition
{

    protected $_jsConditionName;

    protected $_argument;

    public function __construct ($argument)
    {
        $classPath = explode('\\', get_class($this));
        $this->_jsConditionName = 'GFormCondition.' . strtoupper(end($classPath));
        $this->_argument = $argument;
    }

    public function renderJavascript ()
    {
        if ($this->_argument instanceof Condition) {
            return "new GFormCondition({$this->_jsConditionName}, {$this->_argument->renderJavascript()})";
        }
        if (is_array($this->_argument) && isset($this->_argument[0]) && ($this->_argument[0] instanceof Condition)) {
            $parts = Array();
            foreach ($this->_argument as $part) {
                $parts[] = $part->renderJavascript();
            }
            $argument = '[' . implode(', ', $parts) . ']';
        }
        else {
            $argument = json_encode($this->_argument);
        }
        return "new GFormCondition({$this->_jsConditionName}, {$argument})";
    }

    public function Evaluate ($value)
    {
        return true;
    }
}
