<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @category    Gekosale 
 * @package     Gekosale\Core\Form
 * @subpackage  Gekosale\Core\Form\Element
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Form\Element;

abstract class OptionedField extends Field
{

    protected function _FormatOptions_JS ()
    {
        if (! isset($this->_attributes['options']) || ! is_array($this->_attributes['options'])) {
            return '';
        }
        $options = Array();
        foreach ($this->_attributes['options'] as $option) {
            $value = addslashes($option->value);
            $label = addslashes($option->label);
            $options[] = "{sValue: '{$value}', sLabel: '{$label}'}";
        }
        return 'aoOptions: [' . implode(', ', $options) . ']';
    }
}
