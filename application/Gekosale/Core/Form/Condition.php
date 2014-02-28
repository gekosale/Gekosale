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
 * Class Condition
 *
 * @package Gekosale\Core\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
abstract class Condition
{

    protected $_jsConditionName;

    protected $_argument;

    public function __construct($argument)
    {
        $class                  = explode('\\', get_class($this));
        $this->_jsConditionName = 'GFormCondition.' . strtoupper(end($class));
        $this->_argument        = $argument;
    }

    public function Render_JS()
    {
        if ($this->_argument instanceof Condition) {
            return "new GFormCondition({$this->_jsConditionName}, {$this->_argument->Render_JS()})";
        }
        if (is_array($this->_argument) and isset($this->_argument[0]) and ($this->_argument[0] instanceof Condition)) {
            $parts = Array();
            foreach ($this->_argument as $part) {
                $parts[] = $part->Render_JS();
            }
            $argument = '[' . implode(', ', $parts) . ']';
        } else {
            $argument = json_encode($this->_argument);
        }

        return "new GFormCondition({$this->_jsConditionName}, {$argument})";
    }

    /**
     * Evaluates condition value
     *
     * @param $value
     *
     * @return bool
     */
    public function Evaluate($value)
    {
        return true;
    }
}
