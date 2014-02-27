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

use Gekosale\Core\Form\Condition;

/**
 * Class GE
 *
 * @package Gekosale\Core\Form\Conditions
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class GE extends Condition
{
    public function evaluate($value)
    {
        if ($this->_argument instanceof Condition) {
            return false;
        }
        return ($value >= $this->_argument);
    }
}