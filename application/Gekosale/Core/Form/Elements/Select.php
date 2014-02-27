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

class Select extends OptionedField
{

	protected function _PrepareAttributes_JS ()
	{
		$attributes = Array(
			$this->_FormatAttribute_JS('name', 'sName'),
			$this->_FormatAttribute_JS('label', 'sLabel'),
			$this->_FormatAttribute_JS('comment', 'sComment'),
			$this->_FormatAttribute_JS('suffix', 'sSuffix'),
			$this->_FormatAttribute_JS('prefix', 'sPrefix'),
			$this->_FormatAttribute_JS('error', 'sError'),
			$this->_FormatAttribute_JS('selector', 'sSelector'),
			$this->_FormatAttribute_JS('css_attribute', 'sCssAttribute'),
			$this->_FormatAttribute_JS('addable', 'bAddable'),
			$this->_FormatAttribute_JS('onAdd', 'fOnAdd', \FormEngine\FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('add_item_prompt', 'sAddItemPrompt'),
			$this->_FormatRepeatable_JS(),
			$this->_FormatRules_JS(),
			$this->_FormatDependency_JS(),
			$this->_FormatOptions_JS(),
			$this->_FormatDefaults_JS()
		);

		
		return $attributes;
	}

}
