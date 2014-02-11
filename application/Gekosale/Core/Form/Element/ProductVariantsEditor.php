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

class ProductVariantsEditor extends Field
{

    protected $_jsGetAttributeSetsForCategories;

    protected $_jsGetAttributesForSet;

    protected $_jsgetValuesForAttribute;

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        if (! isset($this->_attributes['allow_generate'])) {
            $this->_attributes['allow_generate'] = 1;
        }
        $this->_attributes['category_field'] = $this->_attributes['category']->getName();
        $this->_attributes['price_field'] = $this->_attributes['price']->getName();
        $this->_attributes['vat_field_name'] = $this->_attributes['vat_field']->getName();
        $this->_attributes['vat_values'] = App::getModel('vat/vat')->getVATValuesAll();
        $this->_attributes['suffixes'] = App::getModel('suffix/suffix')->getSuffixTypes();
        $this->_jsGetAttributeSetsForCategories = 'GetAttributeSetsForCategories_' . $this->_id;
        $this->_jsGetAttributesForSet = 'GetAttributesForSet_' . $this->_id;
        $this->_jsgetValuesForAttribute = 'getValuesForAttribute_' . $this->_id;
        $this->_jsGetCartesian = 'GetCartesian_' . $this->_id;
        $this->_jsAddAttribute = 'AddAttribute_' . $this->_id;
        $this->_jsAddValue = 'AddValue_' . $this->_id;
        $this->_attributes['get_sets_for_categories'] = 'xajax_' . $this->_jsGetAttributeSetsForCategories;
        $this->_attributes['get_attributes_for_set'] = 'xajax_' . $this->_jsGetAttributesForSet;
        $this->_attributes['get_values_for_attribute'] = 'xajax_' . $this->_jsgetValuesForAttribute;
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
            $this->_jsgetValuesForAttribute,
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

    protected function prepareAttributesJavascript ()
    {
        $attributes = Array(
            $this->formatAttributeJavascript('name', 'sName'),
            $this->formatAttributeJavascript('label', 'sLabel'),
            $this->formatAttributeJavascript('comment', 'sComment'),
            $this->formatAttributeJavascript('error', 'sError'),
            $this->formatAttributeJavascript('get_sets_for_categories', 'fGetSetsForCategories', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('get_attributes_for_set', 'fGetAttributesForSet', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('get_values_for_attribute', 'fgetValuesForAttribute', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('get_cartesian', 'fGetCartesian', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('add_attribute', 'fAddAttribute', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('add_value', 'fAddValue', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('category_field', 'sCategoryField'),
            $this->formatAttributeJavascript('price_field', 'sPriceField'),
            $this->formatAttributeJavascript('allow_generate', 'bAllowGenerate'),
            $this->formatAttributeJavascript('vat_field_name', 'sVatField'),
            $this->formatAttributeJavascript('vat_values', 'aoVatValues', FE::TYPE_OBJECT),
            $this->formatAttributeJavascript('currency', 'sCurrency'),
            $this->formatAttributeJavascript('photos', 'aoPhotos', FE::TYPE_OBJECT),
            $this->formatAttributeJavascript('availablity', 'aoAvailablity', FE::TYPE_OBJECT),
            $this->formatAttributeJavascript('suffixes', 'aoSuffixes', FE::TYPE_OBJECT),
            $this->formatAttributeJavascript('set', 'sSet'),
            $this->formatRepeatableJavascript(),
            $this->_FormatRules_JS(),
            $this->formatDependencyJavascript(),
            $this->_FormatDefaults_JS()
        );
        return $attributes;
    }
}