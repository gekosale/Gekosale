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

	protected function prepareAttributesJs ()
	{
		$attributes = Array(
			$this->formatAttributeJs('name', 'sName'),
			$this->formatAttributeJs('label', 'sLabel'),
			$this->formatAttributeJs('error', 'sError'),
			$this->formatAttributeJs('set_id', 'sSetId'),
			$this->formatAttributeJs('product_id', 'sProductId'),
			$this->formatAttributeJs('attribute_groups', 'aAttributeGroups', FE::TYPE_OBJECT),
			$this->formatAttributeJs('technical_attributes', 'aTechnicalAttributes', FE::TYPE_OBJECT),
			$this->formatRepeatableJs(),
			$this->formatRulesJs(),
			$this->formatDependencyJs(),
			$this->formatDefaultsJs()
		);
		return $attributes;
	}

	public function SaveAttributeGroup ($request)
	{
		if (substr($request['attributeGroupId'], 0, 3) == 'new'){
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
		if (substr($request['attributeId'], 0, 3) == 'new'){
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
