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

namespace Gekosale\Core\Form\filters;

/**
 * Interface filterInterface
 *
 * @package Gekosale\Core\Form\filters
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
interface filterInterface
{

    /**
     * filters given value
     *
     * @param $value
     *
     * @return mixed
     */
    public function filterValue($value);
} 