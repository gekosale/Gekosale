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

namespace Gekosale\Core\Form\Conditions;

use Gekosale\Core\ConditionInterface;
use Gekosale\Core\Form\Condition;

/**
 * Class Equals
 *
 * @package FormEngine\Conditions
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Equals extends Condition implements ConditionInterface
{

    public function Evaluate($value)
    {
        if ($this->_argument instanceof Condition) {
            return false;
        }
        if (is_array($this->_argument)) {
            return in_array($value, $this->_argument);
        }

        return ($value == $this->_argument);
    }

}