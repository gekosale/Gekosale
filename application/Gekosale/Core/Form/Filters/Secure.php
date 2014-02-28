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

use Gekosale\Core\filters\filterInterface;
use Gekosale\Core\Form\filter;

/**
 * Class Secure
 *
 * @package Gekosale\Core\Form\filters
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Secure extends filter implements filterInterface
{

    protected function filterValue($value)
    {
        return $value;
    }
}
