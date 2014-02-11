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

    protected function prepareAttributesJavascript ()
    {
        $attributes = Array(
            $this->formatAttributeJavascript('name', 'sName'),
            $this->formatAttributeJavascript('label', 'sLabel'),
            $this->formatAttributeJavascript('comment', 'sComment'),
            $this->formatAttributeJavascript('suffix', 'sSuffix'),
            $this->formatAttributeJavascript('price_precision', 'iPricePrecision'),
            $this->formatAttributeJavascript('range_precision', 'iRangePrecision'),
            $this->formatAttributeJavascript('range_suffix', 'sRangeSuffix'),
            $this->formatAttributeJavascript('prefixes', 'asPrefixes'),
            $this->formatAttributeJavascript('allow_vat', 'bAllowVat', FE::TYPE_BOOLEAN),
            $this->formatAttributeJavascript('error', 'sError'),
            $this->formatAttributeJavascript('vat_values', 'aoVatValues', FE::TYPE_OBJECT),
            $this->_FormatOptions_JS(),
            $this->_FormatRules_JS(),
            $this->formatDependencyJavascript(),
            $this->_FormatDefaults_JS()
        );
        return $attributes;
    }
}
