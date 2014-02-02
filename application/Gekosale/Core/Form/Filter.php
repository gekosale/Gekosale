<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @category    Gekosale 
 * @package     Gekosale\Core\Form 
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Form;

abstract class Filter
{

    public function __construct ()
    {
    }

    public function Filter ($values)
    {
        if (is_array($values)) {
            foreach ($values as &$value) {
                $value = $this->Filter($value);
            }
        }
        else {
            $values = $this->_FilterValue($values);
        }
        return $values;
    }

    protected function _FilterValue ($value)
    {
        return $value;
    }
}
