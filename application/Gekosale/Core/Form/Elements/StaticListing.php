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

namespace Gekosale\Core\Form\Elements;

/**
 * Class StaticListing
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class StaticListing extends Field implements ElementInterface
{

    public function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('title', 'sTitle'),
            $this->formatListItemsJs('values', 'aoValues'),
            $this->formatAttributeJs('collapsible', 'bCollapsible', ElementInterface::TYPE_BOOLEAN),
            $this->formatAttributeJs('expanded', 'bExpanded', ElementInterface::TYPE_BOOLEAN),
            $this->formatDependencyJs()
        );

        return $attributes;
    }

    protected function formatListItemsJs($attributeName, $name)
    {
        if (!isset($this->_attributes[$attributeName]) || !is_array($this->_attributes[$attributeName])) {
            return '';
        }
        $options = Array();
        foreach ($this->_attributes[$attributeName] as $option) {
            $value     = addslashes($option->value);
            $label     = addslashes($option->label);
            $options[] = "{sValue: '{$value}', sCaption: '{$label}'}";
        }

        return $name . ': [' . implode(', ', $options) . ']';
    }

    public function renderStatic()
    {
    }

    public function populate($value)
    {
    }

}
