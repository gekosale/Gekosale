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

class FontStyle extends TextField
{

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
	}

	protected function _FormatStyles_JS ()
	{
		
		$options[] = "{sValue: 'Arial,Arial,Helvetica,sans-serif', sLabel: 'Arial'}";
		$options[] = "{sValue: 'Arial Black,Arial Black,Gadget,sans-serif', sLabel: 'Arial Black'}";
		$options[] = "{sValue: 'Comic Sans MS,Comic Sans MS,cursive', sLabel: 'Comic Sans MS'}";
		$options[] = "{sValue: 'Courier New,Courier New,Courier,monospace', sLabel: 'Courier New'}";
		$options[] = "{sValue: 'Georgia,Georgia,serif', sLabel: 'Georgia'}";
		$options[] = "{sValue: 'Impact,Charcoal,sans-serif', sLabel: 'Impact'}";
		$options[] = "{sValue: 'Lucida Console,Monaco,monospace', sLabel: 'Lucida Console'}";
		$options[] = "{sValue: 'Lucida Sans Unicode,Lucida Grande,sans-serif', sLabel: 'Lucida Sans'}";
		$options[] = "{sValue: 'Palatino Linotype,Book Antiqua,Palatino,serif', sLabel: 'Palatino Linotype'}";
		$options[] = "{sValue: 'Tahoma,Geneva,sans-serif', sLabel: 'Tahoma'}";
		$options[] = "{sValue: 'Times New Roman,Times,serif', sLabel: 'Times New Roman'}";
		$options[] = "{sValue: 'Trebuchet MS,Helvetica,sans-serif', sLabel: 'Trebuchet'}";
		$options[] = "{sValue: 'Verdana,Geneva,sans-serif', sLabel: 'Verdana'}";
		
		return 'aoTypes: [' . implode(', ', $options) . ']';
	}

	protected function _PrepareAttributes_JS ()
	{
		$attributes = Array(
			$this->_FormatAttribute_JS('name', 'sName'),
			$this->_FormatAttribute_JS('label', 'sLabel'),
			$this->_FormatAttribute_JS('comment', 'sComment'),
			$this->_FormatAttribute_JS('error', 'sError'),
			$this->_FormatAttribute_JS('selector', 'sSelector'),
			$this->_FormatRules_JS(),
			$this->_FormatStyles_JS(),
			$this->_FormatDependency_JS(),
			$this->_FormatDefaults_JS()
		);
		return $attributes;
	}

}
