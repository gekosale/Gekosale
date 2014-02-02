<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @category    Gekosale 
 * @package     Gekosale\Core\Form 
 * @subpackage  Gekosale\Core\Form\Condition 
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Form\Condition;

use Gekosale\Core\Form\Condition;

class Equals extends Condition
{

    public function Evaluate ($value)
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