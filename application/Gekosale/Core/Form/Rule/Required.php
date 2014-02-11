<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @category    Gekosale 
 * @package     Gekosale\Core\Form 
 * @subpackage  Gekosale\Core\Form\Rule
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Form\Rule;

use Gekosale\Core\Form\Rule;

class Required extends Rule
{

    protected function check ($value)
    {
        if (is_array($value)) {
            return ! empty($value);
        }
        else {
            if (strlen($value) > 0) {
                return true;
            }
            
            return false;
        }
    }
}
