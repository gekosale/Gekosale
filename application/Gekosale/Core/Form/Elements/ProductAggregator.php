<?php
/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the
 * Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it
 * through the
 * world-wide-web, please send an email to license@verison.pl so we can send you
 * a copy immediately.
 */

namespace FormEngine\Elements;
use Exception;
use Gekosale\App as App;
use Gekosale\Translation as Translation;

class ProductAggregator extends Field
{
	protected $_jsFunction;

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
		if (! isset($this->_attributes['products_source_field']) || ! ($this->_attributes['products_source_field'] instanceof ProductSelect)){
			throw new Exception("Source field (attribute: products_source_field) not set for field '{$this->_attributes['name']}'.");
		}
		$this->_attributes['products_source_field_name'] = $this->_attributes['products_source_field']->GetName();
		$this->_attributes['vat_values'] = App::getModel('vat/vat')->getVATAll();
		$this->_attributes['prefixes'] = Array(
			Translation::get('TXT_PRICE_NET'),
			Translation::get('TXT_PRICE_GROSS')
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
		foreach ($request['products'] as $product){
			$products[] = App::getModel('product/product')->getProductVariantDetails($product);
		}
		return Array(
			'products' => $products
		);
	}

	protected function _PrepareAttributes_JS ()
	{
		$attributes = Array(
			$this->_FormatAttribute_JS('name', 'sName'),
			$this->_FormatAttribute_JS('products_source_field_name', 'sProductsSourceField'),
			$this->_FormatAttribute_JS('suffix', 'sSuffix'),
			$this->_FormatAttribute_JS('prefixes', 'asPrefixes'),
			$this->_FormatAttribute_JS('vat_values', 'aoVatValues', \FormEngine\FE::TYPE_OBJECT),
			$this->_FormatAttribute_JS('jsfunction', 'fLoadProductData', \FormEngine\FE::TYPE_FUNCTION)
		);
		return $attributes;
	}
}
