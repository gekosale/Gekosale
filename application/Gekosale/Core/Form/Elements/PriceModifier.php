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
 * Class PriceModifier
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class PriceModifier extends Price implements ElementInterface
{

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        if (!isset($this->_attributes['base_price_field']) || !($this->_attributes['base_price_field'] instanceof Field)) {
            throw new Exception("Base price source field (attribute: base_price_field) not set for field '{$this->_attributes['name']}'.");
        }
        $this->_attributes['base_price_field_name'] = $this->_attributes['base_price_field']->GetName();
        $this->_attributes['suffixes']              = App::getModel('suffix/suffix')->getSuffixTypesForSelect();
    }

    public function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatAttributeJs('comment', 'sComment'),
            $this->formatAttributeJs('suffix', 'sSuffix'),
            $this->formatAttributeJs('prefixes', 'asPrefixes'),
            $this->formatAttributeJs('error', 'sError'),
            $this->formatAttributeJs('vat_field_name', 'sVatField'),
            $this->formatAttributeJs('base_price_field_name', 'sBasePriceField'),
            $this->formatAttributeJs('vat_values', 'aoVatValues', ElementInterface::TYPE_OBJECT),
            $this->formatAttributeJs('suffixes', 'oSuffixes', ElementInterface::TYPE_OBJECT),
            $this->formatRulesJs(),
            $this->formatDependencyJs(),
            $this->formatDefaultsJs()
        );

        return $attributes;
    }
}
