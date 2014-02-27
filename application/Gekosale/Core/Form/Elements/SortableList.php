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

	protected function prepareAttributesJs ()
	{
		$attributes = Array(
			$this->formatAttributeJs('name', 'sName'),
			$this->formatAttributeJs('label', 'sLabel'),
			$this->formatAttributeJs('error', 'sError'),
			$this->formatAttributeJs('clickable', 'bClickable'),
			$this->formatAttributeJs('deletable', 'bDeletable'),
			$this->formatAttributeJs('sortable', 'bSortable'),
			$this->formatAttributeJs('addable', 'bAddable'),
			$this->formatAttributeJs('items', 'oItems', FE::TYPE_OBJECT),
			$this->formatAttributeJs('total', 'iTotal'),
			$this->formatAttributeJs('onClick', 'fOnClick', FE::TYPE_FUNCTION),
			$this->formatAttributeJs('onAdd', 'fOnAdd', FE::TYPE_FUNCTION),
			$this->formatAttributeJs('onAfterAdd', 'fOnAfterAdd', FE::TYPE_FUNCTION),
			$this->formatAttributeJs('onDelete', 'fOnDelete', FE::TYPE_FUNCTION),
			$this->formatAttributeJs('onAfterDelete', 'fOnAfterDelete', FE::TYPE_FUNCTION),
			$this->formatAttributeJs('onSaveOrder', 'fOnSaveOrder', FE::TYPE_FUNCTION),
			$this->formatAttributeJs('active', 'sActive'),
			$this->formatAttributeJs('add_item_prompt', 'sAddItemPrompt'),
			$this->formatAttributeJs('delete_item_prompt', 'sDeleteItemPrompt'),
			$this->formatAttributeJs('set', 'sSet'),
			$this->formatRepeatableJs(),
			$this->formatRulesJs(),
			$this->formatDependencyJs(),
			$this->formatDefaultsJs()
		);
		return $attributes;
	}

}
