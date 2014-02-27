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
use Gekosale\App as App;

class Tree extends Field
{
	
	protected $_jsGetChildren;

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
		$this->_jsGetChildren = 'GetChildren_' . $this->_id;
		if (isset($this->_attributes['load_children']) && is_callable($this->_attributes['load_children'])){
			$this->_attributes['get_children'] = 'xajax_' . $this->_jsGetChildren;
			App::getRegistry()->xajaxInterface->registerFunction(array(
				$this->_jsGetChildren,
				$this,
				'getChildren'
			));
		}
		if (! isset($this->_attributes['addLabel'])){
			$this->_attributes['addLabel'] = \Gekosale\Translation::get('TXT_ADD');
		}
		if (! isset($this->_attributes['retractable'])){
			$this->_attributes['retractable'] = true;
		}
		
		$this->_attributes['total'] = count($this->_attributes['items']);
	}

	public function getChildren ($request)
	{
		$children = call_user_func($this->_attributes['load_children'], $request['parent']);
		if (! is_array($children)){
			$children = Array();
		}
		return Array(
			'children' => $children
		);
	}

	protected function prepareAttributesJs ()
	{
		$attributes = Array(
			$this->formatAttributeJs('name', 'sName'),
			$this->formatAttributeJs('label', 'sLabel'),
			$this->formatAttributeJs('addLabel', 'sAddLabel'),
			$this->formatAttributeJs('error', 'sError'),
			$this->formatAttributeJs('selectable', 'bSelectable'),
			$this->formatAttributeJs('choosable', 'bChoosable'),
			$this->formatAttributeJs('clickable', 'bClickable'),
			$this->formatAttributeJs('deletable', 'bDeletable'),
			$this->formatAttributeJs('sortable', 'bSortable'),
			$this->formatAttributeJs('retractable', 'bRetractable'),
			$this->formatAttributeJs('addable', 'bAddable'),
			$this->formatAttributeJs('total', 'iTotal'),
			$this->formatAttributeJs('restrict', 'iRestrict'),
			$this->formatAttributeJs('items', 'oItems', \FormEngine\FE::TYPE_OBJECT),
			$this->formatAttributeJs('onClick', 'fOnClick', \FormEngine\FE::TYPE_FUNCTION),
			$this->formatAttributeJs('onDuplicate', 'fOnDuplicate', \FormEngine\FE::TYPE_FUNCTION),
			$this->formatAttributeJs('onAdd', 'fOnAdd', \FormEngine\FE::TYPE_FUNCTION),
			$this->formatAttributeJs('onAfterAdd', 'fOnAfterAdd', \FormEngine\FE::TYPE_FUNCTION),
			$this->formatAttributeJs('onDelete', 'fOnDelete', \FormEngine\FE::TYPE_FUNCTION),
			$this->formatAttributeJs('onAfterDelete', 'fOnAfterDelete', \FormEngine\FE::TYPE_FUNCTION),
			$this->formatAttributeJs('onSaveOrder', 'fOnSaveOrder', \FormEngine\FE::TYPE_FUNCTION),
			$this->formatAttributeJs('active', 'sActive'),
			$this->formatAttributeJs('onAfterDeleteId', 'sOnAfterDeleteId'),
			$this->formatAttributeJs('add_item_prompt', 'sAddItemPrompt'),
			$this->formatAttributeJs('get_children', 'fGetChildren', \FormEngine\FE::TYPE_FUNCTION),
			$this->formatAttributeJs('prevent_duplicates', 'bPreventDuplicates', \FormEngine\FE::TYPE_BOOLEAN),
			$this->formatAttributeJs('prevent_duplicates_on_all_levels', 'bPreventDuplicatesOnAllLevels', \FormEngine\FE::TYPE_BOOLEAN),
			$this->formatAttributeJs('set', 'sSet'),
			$this->formatRepeatableJs(),
			$this->formatRulesJs(),
			$this->formatDependencyJs(),
			$this->formatDefaultsJs()
		);
		return $attributes;
	}

}
