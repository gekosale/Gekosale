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

class FieldsetLanguage extends Fieldset
{
	
	protected $languages = Array();

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
		$this->languages = App::getModel('language')->getLanguageALL();
		$count = count($this->languages);
		
		$this->_attributes['repeat_min'] = $count;
		$this->_attributes['repeat_max'] = $count;
	}

	protected function _FormatLanguages_JS ()
	{
		
		$options = Array();
		foreach ($this->languages as $language){
			$value = addslashes($language['id']);
			$label = addslashes($language['translation']);
			$flag = addslashes($language['flag']);
			$options[] = "{sValue: '{$value}', sLabel: '{$label}',sFlag: '{$flag}' }";
		}
		return 'aoLanguages: [' . implode(', ', $options) . ']';
	}

	protected function _PrepareAttributes_JS ()
	{
		$attributes = Array(
			$this->_FormatAttribute_JS('name', 'sName'),
			$this->_FormatAttribute_JS('label', 'sLabel'),
			$this->_FormatRepeatable_JS(),
			$this->_FormatDependency_JS(),
			$this->_FormatLanguages_JS(),
			'aoFields: [' . $this->_RenderChildren() . ']'
		);
		return $attributes;
	}

}
