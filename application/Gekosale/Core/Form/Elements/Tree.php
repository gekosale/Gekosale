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

	protected function _PrepareAttributes_JS ()
	{
		$attributes = Array(
			$this->_FormatAttribute_JS('name', 'sName'),
			$this->_FormatAttribute_JS('label', 'sLabel'),
			$this->_FormatAttribute_JS('addLabel', 'sAddLabel'),
			$this->_FormatAttribute_JS('error', 'sError'),
			$this->_FormatAttribute_JS('selectable', 'bSelectable'),
			$this->_FormatAttribute_JS('choosable', 'bChoosable'),
			$this->_FormatAttribute_JS('clickable', 'bClickable'),
			$this->_FormatAttribute_JS('deletable', 'bDeletable'),
			$this->_FormatAttribute_JS('sortable', 'bSortable'),
			$this->_FormatAttribute_JS('retractable', 'bRetractable'),
			$this->_FormatAttribute_JS('addable', 'bAddable'),
			$this->_FormatAttribute_JS('total', 'iTotal'),
			$this->_FormatAttribute_JS('restrict', 'iRestrict'),
			$this->_FormatAttribute_JS('items', 'oItems', \FormEngine\FE::TYPE_OBJECT),
			$this->_FormatAttribute_JS('onClick', 'fOnClick', \FormEngine\FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('onDuplicate', 'fOnDuplicate', \FormEngine\FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('onAdd', 'fOnAdd', \FormEngine\FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('onAfterAdd', 'fOnAfterAdd', \FormEngine\FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('onDelete', 'fOnDelete', \FormEngine\FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('onAfterDelete', 'fOnAfterDelete', \FormEngine\FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('onSaveOrder', 'fOnSaveOrder', \FormEngine\FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('active', 'sActive'),
			$this->_FormatAttribute_JS('onAfterDeleteId', 'sOnAfterDeleteId'),
			$this->_FormatAttribute_JS('add_item_prompt', 'sAddItemPrompt'),
			$this->_FormatAttribute_JS('get_children', 'fGetChildren', \FormEngine\FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('prevent_duplicates', 'bPreventDuplicates', \FormEngine\FE::TYPE_BOOLEAN),
			$this->_FormatAttribute_JS('prevent_duplicates_on_all_levels', 'bPreventDuplicatesOnAllLevels', \FormEngine\FE::TYPE_BOOLEAN),
			$this->_FormatAttribute_JS('set', 'sSet'),
			$this->_FormatRepeatable_JS(),
			$this->_FormatRules_JS(),
			$this->_FormatDependency_JS(),
			$this->_FormatDefaults_JS()
		);
		return $attributes;
	}

}
