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

namespace Gekosale\Core\Form\Filters;

use Gekosale\Core\Filters\FilterInterface;
use Gekosale\Core\Form\Filter;

/**
 * Class CommaToDotChanger
 *
 * Replaces commas with dots. Required to normalize submitted decimals.
 *
 * @package Gekosale\Core\Form\Filters
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CommaToDotChanger extends Filter implements FilterInterface
{

    protected function _FilterValue($value)
    {
        return str_replace(',', '.', $value);
    }

}