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
use FormEngine\FE as FE;

class SortableList extends Field
{
	
	protected $_jsGetChildren;

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
	}

	protected function _PrepareAttributes_JS ()
	{
		$attributes = Array(
			$this->_FormatAttribute_JS('name', 'sName'),
			$this->_FormatAttribute_JS('label', 'sLabel'),
			$this->_FormatAttribute_JS('error', 'sError'),
			$this->_FormatAttribute_JS('clickable', 'bClickable'),
			$this->_FormatAttribute_JS('deletable', 'bDeletable'),
			$this->_FormatAttribute_JS('sortable', 'bSortable'),
			$this->_FormatAttribute_JS('addable', 'bAddable'),
			$this->_FormatAttribute_JS('items', 'oItems', FE::TYPE_OBJECT),
			$this->_FormatAttribute_JS('total', 'iTotal'),
			$this->_FormatAttribute_JS('onClick', 'fOnClick', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('onAdd', 'fOnAdd', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('onAfterAdd', 'fOnAfterAdd', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('onDelete', 'fOnDelete', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('onAfterDelete', 'fOnAfterDelete', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('onSaveOrder', 'fOnSaveOrder', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('active', 'sActive'),
			$this->_FormatAttribute_JS('add_item_prompt', 'sAddItemPrompt'),
			$this->_FormatAttribute_JS('delete_item_prompt', 'sDeleteItemPrompt'),
			$this->_FormatAttribute_JS('set', 'sSet'),
			$this->_FormatRepeatable_JS(),
			$this->_FormatRules_JS(),
			$this->_FormatDependency_JS(),
			$this->_FormatDefaults_JS()
		);
		return $attributes;
	}

}
