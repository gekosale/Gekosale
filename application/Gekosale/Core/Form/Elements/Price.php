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

class Price extends TextField implements ElementInterface
{

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        if (isset($this->_attributes['vat_field']) && is_subclass_of($this->_attributes['vat_field'], 'FormEngine\Elements\Field')) {
            $this->_attributes['vat_field_name'] = $this->_attributes['vat_field']->getName();
        }
        $this->_attributes['prefixes'] = Array(
            Translation::get('TXT_PRICE_NET'),
            Translation::get('TXT_PRICE_GROSS')
        );
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
            $this->formatRulesJs(),
            $this->formatDependencyJs(),
            $this->formatDefaultsJs()
        );

        return $attributes;
    }

}
