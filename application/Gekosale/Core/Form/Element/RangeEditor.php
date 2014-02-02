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

class RangeEditor extends OptionedField
{

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        if (isset($this->_attributes['allow_vat']) && ! $this->_attributes['allow_vat']) {
            $this->_attributes['vat_values'] = Array();
        }
        else {
            $this->_attributes['vat_values'] = App::getModel('vat/vat')->getVATAllForRangeEditor();
        }
        if (! isset($this->_attributes['range_precision'])) {
            $this->_attributes['range_precision'] = 2;
        }
        if (! isset($this->_attributes['price_precision'])) {
            $this->_attributes['price_precision'] = 2;
        }
        $this->_attributes['prefixes'] = Array(
            $this->trans('TXT_PRICE_NET'),
            $this->trans('TXT_PRICE_GROSS')
        );
    }

    protected function _PrepareAttributes_JS ()
    {
        $attributes = Array(
            $this->_FormatAttribute_JS('name', 'sName'),
            $this->_FormatAttribute_JS('label', 'sLabel'),
            $this->_FormatAttribute_JS('comment', 'sComment'),
            $this->_FormatAttribute_JS('suffix', 'sSuffix'),
            $this->_FormatAttribute_JS('price_precision', 'iPricePrecision'),
            $this->_FormatAttribute_JS('range_precision', 'iRangePrecision'),
            $this->_FormatAttribute_JS('range_suffix', 'sRangeSuffix'),
            $this->_FormatAttribute_JS('prefixes', 'asPrefixes'),
            $this->_FormatAttribute_JS('allow_vat', 'bAllowVat', FE::TYPE_BOOLEAN),
            $this->_FormatAttribute_JS('error', 'sError'),
            $this->_FormatAttribute_JS('vat_values', 'aoVatValues', FE::TYPE_OBJECT),
            $this->_FormatOptions_JS(),
            $this->_FormatRules_JS(),
            $this->_FormatDependency_JS(),
            $this->_FormatDefaults_JS()
        );
        return $attributes;
    }
}
