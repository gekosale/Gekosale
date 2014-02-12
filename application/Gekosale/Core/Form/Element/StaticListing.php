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

class StaticListing extends Field
{

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
    }

    protected function prepareAttributesJavascript ()
    {
        $attributes = Array(
            $this->formatAttributeJavascript('label', 'sLabel'),
            $this->formatAttributeJavascript('name', 'sName'),
            $this->formatAttributeJavascript('title', 'sTitle'),
            $this->_FormatListItems_JS('values', 'aoValues'),
            $this->formatAttributeJavascript('collapsible', 'bCollapsible', FE::TYPE_BOOLEAN),
            $this->formatAttributeJavascript('expanded', 'bExpanded', FE::TYPE_BOOLEAN),
            $this->formatDependencyJavascript()
        );
        return $attributes;
    }

    protected function _FormatListItems_JS ($attributeName, $name)
    {
        if (! isset($this->_attributes[$attributeName]) || ! is_array($this->_attributes[$attributeName])) {
            return '';
        }
        $options = Array();
        foreach ($this->_attributes[$attributeName] as $option) {
            $value = addslashes($option->value);
            $label = addslashes($option->label);
            $options[] = "{sValue: '{$value}', sCaption: '{$label}'}";
        }
        return $name . ': [' . implode(', ', $options) . ']';
    }

    public function renderStatic ()
    {
    }

    public function populate ($value)
    {
    }
}
