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

    /**
     * @param $values
     *
     * @return mixed
     */
    public function filter($values)
    {
        if (is_array($values)) {
            foreach ($values as &$value) {
                $value = $this->filter($value);
            }
        } else {
            $values = $this->filterValue($values);
        }
        return $values;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    protected function filterValue($value)
    {
        return $value;
    }

}
