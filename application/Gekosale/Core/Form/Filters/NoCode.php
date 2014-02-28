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

use Gekosale\Core\Form\Filter;

/**
 * Class NoCode
 *
 * Strips all html tags from submitted values
 *
 * @package Gekosale\Core\Form\Filters
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class NoCode extends Filter implements FilterInterface
{

    public function filterValue($value)
    {
        return strip_tags($value);
    }

}
