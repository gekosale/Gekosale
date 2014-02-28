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

use Gekosale\Core\Form\Rule;

class Required extends Rule implements RuleInterface
{

    public function checkValue($value)
    {
        if (is_array($value)) {
            return !empty($value);
        } else {
            if (strlen($value) > 0) {
                return true;
            }

            return false;
        }
    }
}
