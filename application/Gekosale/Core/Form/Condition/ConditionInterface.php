<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @package     Gekosale\Core\Form 
 * @subpackage  Gekosale\Core\Form\Condition 
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Form\Condition;

interface ConditionInterface
{

    /**
     * Evaluate the value for condition
     * @param string $value
     */
    public function Evaluate ($value);
}