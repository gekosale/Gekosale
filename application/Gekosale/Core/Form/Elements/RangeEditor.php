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
 * Class RangeEditor
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class RangeEditor extends OptionedField implements ElementInterface
{

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        if (isset($this->attributes['allow_vat']) && !$this->attributes['allow_vat']) {
            $this->attributes['vat_values'] = [];
        } else {
            $this->attributes['vat_values'] = $attributes['vat_values'];
        }

        if (!isset($this->attributes['range_precision'])) {
            $this->attributes['range_precision'] = 2;
        }

        if (!isset($this->attributes['price_precision'])) {
            $this->attributes['price_precision'] = 2;
        }
    }

    public function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatAttributeJs('comment', 'sComment'),
            $this->formatAttributeJs('suffix', 'sSuffix'),
            $this->formatAttributeJs('price_precision', 'iPricePrecision'),
            $this->formatAttributeJs('range_precision', 'iRangePrecision'),
            $this->formatAttributeJs('range_suffix', 'sRangeSuffix'),
            $this->formatAttributeJs('prefixes', 'asPrefixes'),
            $this->formatAttributeJs('allow_vat', 'bAllowVat', ElementInterface::TYPE_BOOLEAN),
            $this->formatAttributeJs('error', 'sError'),
            $this->formatAttributeJs('vat_values', 'aoVatValues', ElementInterface::TYPE_OBJECT),
            $this->formatOptionsJs(),
            $this->formatRulesJs(),
            $this->formatDependencyJs(),
            $this->formatDefaultsJs()
        );

        return $attributes;
    }
}
