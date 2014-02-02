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

class TechnicalDataEditor extends Field
{

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        $this->_attributes['attribute_groups'] = $this->GetAttributeGroups();
        $this->_attributes['technical_attributes'] = $this->GetTechnicalAttributes();
        $this->_RegisterXajaxMethod('fGetTechnicalAttributesForSet', Array(
            $this,
            'GetTechnicalAttributesForSet'
        ));
        $this->_RegisterXajaxMethod('fGetSets', Array(
            $this,
            'GetSets'
        ));
        $this->_RegisterXajaxMethod('fSaveSet', Array(
            $this,
            'SaveSet'
        ));
        $this->_RegisterXajaxMethod('fDeleteSet', Array(
            $this,
            'DeleteSet'
        ));
        $this->_RegisterXajaxMethod('fSaveAttributeGroup', Array(
            $this,
            'SaveAttributeGroup'
        ));
        $this->_RegisterXajaxMethod('fDeleteAttributeGroup', Array(
            $this,
            'DeleteAttributeGroup'
        ));
        $this->_RegisterXajaxMethod('fSaveAttribute', Array(
            $this,
            'SaveAttribute'
        ));
        $this->_RegisterXajaxMethod('fDeleteAttribute', Array(
            $this,
            'DeleteAttribute'
        ));
        $this->_RegisterXajaxMethod('fGetValuesForAttribute', Array(
            $this,
            'GetValuesForAttribute'
        ));
    }

    protected function _PrepareAttributes_JS ()
    {
        $attributes = Array(
            $this->_FormatAttribute_JS('name', 'sName'),
            $this->_FormatAttribute_JS('label', 'sLabel'),
            $this->_FormatAttribute_JS('error', 'sError'),
            $this->_FormatAttribute_JS('set_id', 'sSetId'),
            $this->_FormatAttribute_JS('product_id', 'sProductId'),
            $this->_FormatAttribute_JS('attribute_groups', 'aAttributeGroups', FE::TYPE_OBJECT),
            $this->_FormatAttribute_JS('technical_attributes', 'aTechnicalAttributes', FE::TYPE_OBJECT),
            $this->_FormatRepeatable_JS(),
            $this->_FormatRules_JS(),
            $this->_FormatDependency_JS(),
            $this->_FormatDefaults_JS()
        );
        return $attributes;
    }

    public function SaveAttributeGroup ($request)
    {
        if (substr($request['attributeGroupId'], 0, 3) == 'new') {
            $request['attributeGroupId'] = 'new';
        }
        return Array(
            'attributeGroupId' => App::GetModel('TechnicalData')->SaveGroup($request['attributeGroupId'], $request['attributeGroupName'])
        );
    }

    public function DeleteAttributeGroup ($request)
    {
        App::GetModel('TechnicalData')->DeleteGroup($request['attributeGroupId']);
        return Array(
            'attributeGroupId' => $request['attributeGroupId']
        );
    }

    public function SaveAttribute ($request)
    {
        if (substr($request['attributeId'], 0, 3) == 'new') {
            $request['attributeId'] = 'new';
        }
        return Array(
            'attributeId' => App::GetModel('TechnicalData')->SaveAttribute($request['attributeId'], $request['attributeName'], $request['attributeType'])
        );
    }

    public function DeleteAttribute ($request)
    {
        App::GetModel('TechnicalData')->DeleteAttribute($request['attributeId']);
        return Array(
            'attributeId' => $request['attributeId']
        );
    }

    public function SaveSet ($request)
    {
        $setId = App::GetModel('TechnicalData')->SaveSet($request['setId'], $request['setName'], $request['setData']);
        return Array(
            'setId' => $setId
        );
    }

    public function DeleteSet ($request)
    {
        App::GetModel('TechnicalData')->DeleteSet($request['setId']);
        return Array(
            'setId' => $request['setId']
        );
    }

    public function GetSets ($request)
    {
        return Array(
            'aoSets' => App::GetModel('TechnicalData')->GetSets($request['productId'], $request['categoryIds'])
        );
    }

    public function GetAttributeGroups ()
    {
        return App::GetModel('TechnicalData')->GetGroups();
    }

    public function GetTechnicalAttributes ()
    {
        return App::GetModel('TechnicalData')->GetAttributes();
    }

    public function GetValuesForAttribute ($request)
    {
        $request['attributeId'];
        return Array(
            Array(
                1 => 'Różowy'
            ),
            Array(
                1 => '60'
            ),
            Array(
                1 => '800x600'
            )
        );
    }

    public function GetTechnicalAttributesForSet ($request)
    {
        return Array(
            'setId' => $request['setId'],
            'aoAttributes' => App::getModel('TechnicalData')->GetSetData($request['setId'])
        );
    }
}
