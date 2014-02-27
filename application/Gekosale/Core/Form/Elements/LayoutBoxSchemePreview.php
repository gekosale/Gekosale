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

class LayoutBoxSchemePreview extends Field
{

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
		if (! isset($this->_attributes['name'])){
			$this->_attributes['name'] = 'LayoutBoxSchemePreview_' . $this->_id;
		}
		if (isset($this->_attributes['layout_box_tpl']) && is_file($this->_attributes['layout_box_tpl'])){
			$this->_attributes['layout_box_tpl'] = file_get_contents($this->_attributes['layout_box_tpl']);
		}
	}

	protected function _PrepareAttributes_JS ()
	{
		$attributes = Array(
			$this->_FormatAttribute_JS('name', 'sName'),
			$this->_FormatAttribute_JS('triggers', 'asTriggers'),
			$this->_FormatAttribute_JS('layout_box_tpl', 'sLayoutBoxTpl'),
			$this->_FormatAttribute_JS('box_scheme', 'sBoxScheme'),
			$this->_FormatAttribute_JS('box_name', 'sBoxName'),
			$this->_FormatAttribute_JS('box_title', 'sBoxTitle'),
			$this->_FormatAttribute_JS('box_content', 'sBoxContent'),
			$this->_FormatAttribute_JS('stylesheets', 'asStylesheets'),
			$this->_FormatDependency_JS()
		);
		return $attributes;
	}

	public function Render_Static ()
	{
	}

	public function Populate ($value)
	{
	}

}
