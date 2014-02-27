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

/**
 * Interface ConditionInterface
 *
 * @package Gekosale\Core\Form\Conditions
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
interface ConditionInterface
{

    /**
     * Evaluates condition value
     *
     * @param $value
     *
     * @return mixed
     */
    public function Evaluate($value);
} 