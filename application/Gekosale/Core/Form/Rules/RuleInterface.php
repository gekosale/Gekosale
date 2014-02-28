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

namespace Gekosale\Core\Form\Rules;

/**
 * Interface RuleInterface
 *
 * @package Gekosale\Core\Form\Rules
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
interface RuleInterface
{

    /**
     * Checks value against requirements
     *
     * @param $value
     *
     * @return bool
     */
    public function _Check($value);

    /**
     * renders rules javascript part
     *
     * @return mixed
     */
    public function render();
} 