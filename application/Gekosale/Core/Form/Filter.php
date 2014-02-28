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

namespace Gekosale\Core\Form;

/**
 * Class Filter
 *
 * @package Gekosale\Core\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
abstract class Filter
{
    public function Filter($values)
    {
        if (is_array($values)) {
            foreach ($values as &$value) {
                $value = $this->Filter($value);
            }
        } else {
            $values = $this->_FilterValue($values);
        }

        return $values;
    }

    protected function _FilterValue($value)
    {
        return $value;
    }

}
