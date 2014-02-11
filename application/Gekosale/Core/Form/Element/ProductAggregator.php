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

class ProductAggregator extends Field
{

    protected $_jsFunction;

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        if (! isset($this->_attributes['products_source_field']) || ! ($this->_attributes['products_source_field'] instanceof ProductSelect)) {
            throw new Exception("Source field (attribute: products_source_field) not set for field '{$this->_attributes['name']}'.");
        }
        $this->_attributes['products_source_field_name'] = $this->_attributes['products_source_field']->getName();
        $this->_attributes['vat_values'] = App::getModel('vat/vat')->getVATAll();
        $this->_attributes['prefixes'] = Array(
            $this->trans('TXT_PRICE_NET'),
            $this->trans('TXT_PRICE_GROSS')
        );
        $this->_jsFunction = 'LoadProductDataForAggregation_' . $this->_id;
        $this->_attributes['jsfunction'] = 'xajax_' . $this->_jsFunction;
        App::getRegistry()->xajaxInterface->registerFunction(array(
            $this->_jsFunction,
            $this,
            'loadProductData'
        ));
    }

    public function loadProductData ($request)
    {
        $products = Array();
        foreach ($request['products'] as $product) {
            $products[] = App::getModel('product/product')->getProductVariantDetails($product);
        }
        return Array(
            'products' => $products
        );
    }

    protected function prepareAttributesJavascript ()
    {
        $attributes = Array(
            $this->formatAttributeJavascript('name', 'sName'),
            $this->formatAttributeJavascript('products_source_field_name', 'sProductsSourceField'),
            $this->formatAttributeJavascript('suffix', 'sSuffix'),
            $this->formatAttributeJavascript('prefixes', 'asPrefixes'),
            $this->formatAttributeJavascript('vat_values', 'aoVatValues', \FormEngine\FE::TYPE_OBJECT),
            $this->formatAttributeJavascript('jsfunction', 'fLoadProductData', \FormEngine\FE::TYPE_FUNCTION)
        );
        return $attributes;
    }
}
