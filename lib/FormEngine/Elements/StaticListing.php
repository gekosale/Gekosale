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

class StaticListing extends Field
{

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
	}

	protected function _PrepareAttributes_JS ()
	{
		$attributes = Array(
			$this->_FormatAttribute_JS('label', 'sLabel'),
			$this->_FormatAttribute_JS('name', 'sName'),
			$this->_FormatAttribute_JS('title', 'sTitle'),
			$this->_FormatListItems_JS('values', 'aoValues'),
			$this->_FormatAttribute_JS('collapsible', 'bCollapsible', FE::TYPE_BOOLEAN),
			$this->_FormatAttribute_JS('expanded', 'bExpanded', FE::TYPE_BOOLEAN),
			$this->_FormatDependency_JS()
		);
		return $attributes;
	}

	protected function _FormatListItems_JS ($attributeName, $name)
	{
		if (! isset($this->_attributes[$attributeName]) || ! is_array($this->_attributes[$attributeName])){
			return '';
		}
		$options = Array();
		foreach ($this->_attributes[$attributeName] as $option){
			$value = addslashes($option->value);
			$label = addslashes($option->label);
			$options[] = "{sValue: '{$value}', sCaption: '{$label}'}";
		}
		return $name . ': [' . implode(', ', $options) . ']';
	}

	public function Render_Static ()
	{
	}

	public function Populate ($value)
	{
	}

}
