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

class PriceModifier extends Price
{

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        if (! isset($this->_attributes['base_price_field']) || ! ($this->_attributes['base_price_field'] instanceof Field)) {
            throw new Exception("Base price source field (attribute: base_price_field) not set for field '{$this->_attributes['name']}'.");
        }
        $this->_attributes['base_price_field_name'] = $this->_attributes['base_price_field']->GetName();
        $this->_attributes['suffixes'] = App::getModel('suffix/suffix')->getSuffixTypesForSelect();
    }

    protected function _PrepareAttributes_JS ()
    {
        $attributes = Array(
            $this->_FormatAttribute_JS('name', 'sName'),
            $this->_FormatAttribute_JS('label', 'sLabel'),
            $this->_FormatAttribute_JS('comment', 'sComment'),
            $this->_FormatAttribute_JS('suffix', 'sSuffix'),
            $this->_FormatAttribute_JS('prefixes', 'asPrefixes'),
            $this->_FormatAttribute_JS('error', 'sError'),
            $this->_FormatAttribute_JS('vat_field_name', 'sVatField'),
            $this->_FormatAttribute_JS('base_price_field_name', 'sBasePriceField'),
            $this->_FormatAttribute_JS('vat_values', 'aoVatValues', \FormEngine\FE::TYPE_OBJECT),
            $this->_FormatAttribute_JS('suffixes', 'oSuffixes', \FormEngine\FE::TYPE_OBJECT),
            $this->_FormatRules_JS(),
            $this->_FormatDependency_JS(),
            $this->_FormatDefaults_JS()
        );
        return $attributes;
    }
}
