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
        $this->registerXajaxMethod('fGetTechnicalAttributesForSet', Array(
            $this,
            'GetTechnicalAttributesForSet'
        ));
        $this->registerXajaxMethod('fGetSets', Array(
            $this,
            'GetSets'
        ));
        $this->registerXajaxMethod('fSaveSet', Array(
            $this,
            'SaveSet'
        ));
        $this->registerXajaxMethod('fDeleteSet', Array(
            $this,
            'DeleteSet'
        ));
        $this->registerXajaxMethod('fSaveAttributeGroup', Array(
            $this,
            'SaveAttributeGroup'
        ));
        $this->registerXajaxMethod('fDeleteAttributeGroup', Array(
            $this,
            'DeleteAttributeGroup'
        ));
        $this->registerXajaxMethod('fSaveAttribute', Array(
            $this,
            'SaveAttribute'
        ));
        $this->registerXajaxMethod('fDeleteAttribute', Array(
            $this,
            'DeleteAttribute'
        ));
        $this->registerXajaxMethod('fGetValuesForAttribute', Array(
            $this,
            'GetValuesForAttribute'
        ));
    }

    protected function prepareAttributesJavascript ()
    {
        $attributes = Array(
            $this->formatAttributeJavascript('name', 'sName'),
            $this->formatAttributeJavascript('label', 'sLabel'),
            $this->formatAttributeJavascript('error', 'sError'),
            $this->formatAttributeJavascript('set_id', 'sSetId'),
            $this->formatAttributeJavascript('product_id', 'sProductId'),
            $this->formatAttributeJavascript('attribute_groups', 'aAttributeGroups', FE::TYPE_OBJECT),
            $this->formatAttributeJavascript('technical_attributes', 'aTechnicalAttributes', FE::TYPE_OBJECT),
            $this->formatRepeatableJavascript(),
            $this->_FormatRules_JS(),
            $this->formatDependencyJavascript(),
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
