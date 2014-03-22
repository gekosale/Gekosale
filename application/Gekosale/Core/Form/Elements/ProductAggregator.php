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
 * Class ProductAggregator
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProductAggregator extends Field implements ElementInterface
{
    protected $_jsFunction;

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        if (!isset($this->attributes['products_source_field']) || !($this->attributes['products_source_field'] instanceof ProductSelect)) {
            throw new Exception("Source field (attribute: products_source_field) not set for field '{$this->attributes['name']}'.");
        }
        $this->attributes['products_source_field_name'] = $this->attributes['products_source_field']->getName();
        $this->attributes['vat_values']                 = App::getModel('vat/vat')->getVATAll();
        $this->attributes['prefixes']                   = Array(
            Translation::get('TXT_PRICE_NET'),
            Translation::get('TXT_PRICE_GROSS')
        );
        $this->_jsFunction                               = 'LoadProductDataForAggregation_' . $this->_id;
        $this->attributes['jsfunction']                 = 'xajax_' . $this->_jsFunction;
        App::getRegistry()->xajaxInterface->registerFunction(array(
            $this->_jsFunction,
            $this,
            'loadProductData'
        ));
    }

    public function loadProductData($request)
    {
        $products = Array();
        foreach ($request['products'] as $product) {
            $products[] = App::getModel('product/product')->getProductVariantDetails($product);
        }

        return Array(
            'products' => $products
        );
    }

    public function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('products_source_field_name', 'sProductsSourceField'),
            $this->formatAttributeJs('suffix', 'sSuffix'),
            $this->formatAttributeJs('prefixes', 'asPrefixes'),
            $this->formatAttributeJs('vat_values', 'aoVatValues', ElementInterface::TYPE_OBJECT),
            $this->formatAttributeJs('jsfunction', 'fLoadProductData', ElementInterface::TYPE_FUNCTION)
        );

        return $attributes;
    }
}
