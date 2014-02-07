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

class Option
{

    public $value;

    public $label;

    public function __construct ($value, $label)
    {
        $this->value = $value;
        $this->label = $label;
    }

    public static function Make ($array, $default = '')
    {
        $result = Array();
        if ($default && is_array($default)) {
            $result[] = new self('', $default[0]);
        }
        foreach ($array as $key => $value) {
            $result[] = new self($key, $value);
        }
        return $result;
    }

    public function __toString ()
    {
        $value = addslashes($this->value);
        $label = addslashes($this->label);
        return "{sValue: '{$value}', sLabel: '{$label}'}";
    }
}
