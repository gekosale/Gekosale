<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms 
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the 
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace FormEngine\Elements;

use Gekosale\App as App;
use FormEngine\FE as FE;

class ProductVariantsEditor extends Field
{
	protected $_jsGetAttributeSetsForCategories;
	protected $_jsGetAttributesForSet;
	protected $_jsGetValuesForAttribute;

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
		if (! isset($this->_attributes['allow_generate'])){
			$this->_attributes['allow_generate'] = 1;
		}
		$this->_attributes['category_field'] = $this->_attributes['category']->GetName();
		$this->_attributes['price_field'] = $this->_attributes['price']->GetName();
		$this->_attributes['vat_field_name'] = $this->_attributes['vat_field']->GetName();
		$this->_attributes['vat_values'] = App::getModel('vat/vat')->getVATValuesAll();
		$this->_attributes['suffixes'] = App::getModel('suffix/suffix')->getSuffixTypes();
		$this->_jsGetAttributeSetsForCategories = 'GetAttributeSetsForCategories_' . $this->_id;
		$this->_jsGetAttributesForSet = 'GetAttributesForSet_' . $this->_id;
		$this->_jsGetValuesForAttribute = 'GetValuesForAttribute_' . $this->_id;
		$this->_jsGetCartesian = 'GetCartesian_' . $this->_id;
		$this->_jsAddAttribute = 'AddAttribute_' . $this->_id;
		$this->_jsAddValue = 'AddValue_' . $this->_id;
		$this->_attributes['get_sets_for_categories'] = 'xajax_' . $this->_jsGetAttributeSetsForCategories;
		$this->_attributes['get_attributes_for_set'] = 'xajax_' . $this->_jsGetAttributesForSet;
		$this->_attributes['get_values_for_attribute'] = 'xajax_' . $this->_jsGetValuesForAttribute;
		$this->_attributes['get_cartesian'] = 'xajax_' . $this->_jsGetCartesian;
		$this->_attributes['add_attribute'] = 'xajax_' . $this->_jsAddAttribute;
		$this->_attributes['add_value'] = 'xajax_' . $this->_jsAddValue;
		App::getRegistry()->xajaxInterface->registerFunction(array(
			$this->_jsGetAttributeSetsForCategories,
			$this,
			'getAttributeSetsForCategories'
		));
		App::getRegistry()->xajaxInterface->registerFunction(array(
			$this->_jsGetAttributesForSet,
			$this,
			'getAttributesForSet'
		));
		App::getRegistry()->xajaxInterface->registerFunction(array(
			$this->_jsGetValuesForAttribute,
			$this,
			'getValuesForAttribute'
		));
		App::getRegistry()->xajaxInterface->registerFunction(array(
			$this->_jsGetCartesian,
			$this,
			'getCartesian'
		));
		App::getRegistry()->xajaxInterface->registerFunction(array(
			$this->_jsAddAttribute,
			$this,
			'addAttribute'
		));
		App::getRegistry()->xajaxInterface->registerFunction(array(
			$this->_jsAddValue,
			$this,
			'addValue'
		));
	}

	public function addAttribute ($request)
	{
		$attributeId = App::getModel('attributeproduct/attributeproduct')->addAttributeGroupName($request['attribute']);
		App::getModel('attributegroup/attributegroup')->addAttributeToGroup($request['set'], $attributeId);
	}

	public function addValue ($request)
	{
		App::getModel('attributeproduct/attributeproduct')->addAttributeGroupValues(Array(
			$request['value']
		), $request['attribute']);
	}

	public function getAttributeSetsForCategories ($request)
	{
		return Array(
			'sets' => App::getModel('attributegroup/attributegroup')->getGroupsForCategory($request['id'])
		);
	}

	public function getAttributesForSet ($request)
	{
		return Array(
			'attributes' => App::getModel('attributegroup/attributegroup')->getAttributesForGroup($request['id'])
		);
	}

	public function getCartesian ($request)
	{
		return Array(
			'variants' => App::getModel('product')->doAJAXCreateCartesianVariants($request)
		);
	}

	public function getValuesForAttribute ($request)
	{
		return Array(
			'values' => App::getModel('attributeproduct/attributeproduct')->getAttributeProductValuesByAttributeGroupId($request['id'])
		);
	}

	protected function _PrepareAttributes_JS ()
	{
		$attributes = Array(
			$this->_FormatAttribute_JS('name', 'sName'),
			$this->_FormatAttribute_JS('label', 'sLabel'),
			$this->_FormatAttribute_JS('comment', 'sComment'),
			$this->_FormatAttribute_JS('error', 'sError'),
			$this->_FormatAttribute_JS('get_sets_for_categories', 'fGetSetsForCategories', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('get_attributes_for_set', 'fGetAttributesForSet', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('get_values_for_attribute', 'fGetValuesForAttribute', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('get_cartesian', 'fGetCartesian', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('add_attribute', 'fAddAttribute', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('add_value', 'fAddValue', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('category_field', 'sCategoryField'),
			$this->_FormatAttribute_JS('price_field', 'sPriceField'),
			$this->_FormatAttribute_JS('allow_generate', 'bAllowGenerate'),
			$this->_FormatAttribute_JS('vat_field_name', 'sVatField'),
			$this->_FormatAttribute_JS('vat_values', 'aoVatValues', FE::TYPE_OBJECT),
			$this->_FormatAttribute_JS('currency', 'sCurrency'),
			$this->_FormatAttribute_JS('photos', 'aoPhotos', FE::TYPE_OBJECT),
			$this->_FormatAttribute_JS('availablity', 'aoAvailablity', FE::TYPE_OBJECT),
			$this->_FormatAttribute_JS('suffixes', 'aoSuffixes', FE::TYPE_OBJECT),
			$this->_FormatAttribute_JS('set', 'sSet'),
			$this->_FormatRepeatable_JS(),
			$this->_FormatRules_JS(),
			$this->_FormatDependency_JS(),
			$this->_FormatDefaults_JS()
		);
		return $attributes;
	}
}