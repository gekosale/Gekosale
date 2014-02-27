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

	protected function prepareAttributesJavascript ()
	{
		$attributes = Array(
			$this->formatAttributeJavascript('name', 'sName'),
			$this->formatAttributeJavascript('triggers', 'asTriggers'),
			$this->formatAttributeJavascript('layout_box_tpl', 'sLayoutBoxTpl'),
			$this->formatAttributeJavascript('box_scheme', 'sBoxScheme'),
			$this->formatAttributeJavascript('box_name', 'sBoxName'),
			$this->formatAttributeJavascript('box_title', 'sBoxTitle'),
			$this->formatAttributeJavascript('box_content', 'sBoxContent'),
			$this->formatAttributeJavascript('stylesheets', 'asStylesheets'),
			$this->formatDependencyJavascript()
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
