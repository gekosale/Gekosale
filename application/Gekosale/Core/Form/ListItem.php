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
 * Class ListItem
 *
 * @package Gekosale\Core\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ListItem
{

    public $value;
    public $label;

    public function __construct($label, $value)
    {
        $this->value = $value;
        $this->label = $label;
    }

    public static function make($array, $default = '')
    {
        $result = Array();
        if ($default && is_array($default)) {
            $result[] = new ListItem($default[0], '');
        }
        foreach ($array as $key => $value) {
            $result[] = new ListItem($key, $value);
        }

        return $result;
    }

}
