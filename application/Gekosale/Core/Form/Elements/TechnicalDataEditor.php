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
 * Class TechnicalDataEditor
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class TechnicalDataEditor extends Field implements ElementInterface
{

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        $this->_attributes['attribute_groups']     = $this->GetAttributeGroups();
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
        $this->_RegisterXajaxMethod('fgetValuesForAttribute', Array(
            $this,
            'getValuesForAttribute'
        ));
    }

    protected function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatAttributeJs('error', 'sError'),
            $this->formatAttributeJs('set_id', 'sSetId'),
            $this->formatAttributeJs('product_id', 'sProductId'),
            $this->formatAttributeJs('attribute_groups', 'aAttributeGroups', ElementInterface::TYPE_OBJECT),
            $this->formatAttributeJs('technical_attributes', 'aTechnicalAttributes', ElementInterface::TYPE_OBJECT),
            $this->formatRepeatableJs(),
            $this->formatRulesJs(),
            $this->formatDependencyJs(),
            $this->formatDefaultsJs()
        );

        return $attributes;
    }

    public function SaveAttributeGroup($request)
    {
        if (substr($request['attributeGroupId'], 0, 3) == 'new') {
            $request['attributeGroupId'] = 'new';
        }

        return Array(
            'attributeGroupId' => App::GetModel('TechnicalData')->SaveGroup($request['attributeGroupId'], $request['attributeGroupName'])
        );
    }

    public function DeleteAttributeGroup($request)
    {
        App::GetModel('TechnicalData')->DeleteGroup($request['attributeGroupId']);

        return Array(
            'attributeGroupId' => $request['attributeGroupId']
        );
    }

    public function SaveAttribute($request)
    {
        if (substr($request['attributeId'], 0, 3) == 'new') {
            $request['attributeId'] = 'new';
        }

        return Array(
            'attributeId' => App::GetModel('TechnicalData')->SaveAttribute($request['attributeId'], $request['attributeName'], $request['attributeType'])
        );
    }

    public function DeleteAttribute($request)
    {
        App::GetModel('TechnicalData')->DeleteAttribute($request['attributeId']);

        return Array(
            'attributeId' => $request['attributeId']
        );
    }

    public function SaveSet($request)
    {
        $setId = App::GetModel('TechnicalData')->SaveSet($request['setId'], $request['setName'], $request['setData']);

        return Array(
            'setId' => $setId
        );
    }

    public function DeleteSet($request)
    {
        App::GetModel('TechnicalData')->DeleteSet($request['setId']);

        return Array(
            'setId' => $request['setId']
        );
    }

    public function GetSets($request)
    {
        return Array(
            'aoSets' => App::GetModel('TechnicalData')->GetSets($request['productId'], $request['categoryIds'])
        );
    }

    public function GetAttributeGroups()
    {
        return App::GetModel('TechnicalData')->GetGroups();
    }

    public function GetTechnicalAttributes()
    {
        return App::GetModel('TechnicalData')->GetAttributes();
    }

    public function getValuesForAttribute($request)
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

    public function GetTechnicalAttributesForSet($request)
    {
        return Array(
            'setId'        => $request['setId'],
            'aoAttributes' => App::getModel('TechnicalData')->GetSetData($request['setId'])
        );
    }

}
