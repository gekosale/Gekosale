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

	protected function prepareAttributesJavascript ()
	{
		$attributes = Array(
			$this->formatAttributeJavascript('name', 'sName'),
			$this->formatAttributeJavascript('label', 'sLabel'),
			$this->formatAttributeJavascript('error', 'sError'),
			$this->formatAttributeJavascript('clickable', 'bClickable'),
			$this->formatAttributeJavascript('deletable', 'bDeletable'),
			$this->formatAttributeJavascript('sortable', 'bSortable'),
			$this->formatAttributeJavascript('addable', 'bAddable'),
			$this->formatAttributeJavascript('items', 'oItems', FE::TYPE_OBJECT),
			$this->formatAttributeJavascript('total', 'iTotal'),
			$this->formatAttributeJavascript('onClick', 'fOnClick', FE::TYPE_FUNCTION),
			$this->formatAttributeJavascript('onAdd', 'fOnAdd', FE::TYPE_FUNCTION),
			$this->formatAttributeJavascript('onAfterAdd', 'fOnAfterAdd', FE::TYPE_FUNCTION),
			$this->formatAttributeJavascript('onDelete', 'fOnDelete', FE::TYPE_FUNCTION),
			$this->formatAttributeJavascript('onAfterDelete', 'fOnAfterDelete', FE::TYPE_FUNCTION),
			$this->formatAttributeJavascript('onSaveOrder', 'fOnSaveOrder', FE::TYPE_FUNCTION),
			$this->formatAttributeJavascript('active', 'sActive'),
			$this->formatAttributeJavascript('add_item_prompt', 'sAddItemPrompt'),
			$this->formatAttributeJavascript('delete_item_prompt', 'sDeleteItemPrompt'),
			$this->formatAttributeJavascript('set', 'sSet'),
			$this->formatRepeatableJavascript(),
			$this->formatRulesJavascript(),
			$this->formatDependencyJavascript(),
			$this->formatDefaultsJavascript()
		);
		return $attributes;
	}

}
