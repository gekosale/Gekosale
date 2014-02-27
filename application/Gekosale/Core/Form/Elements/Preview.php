<?php defined('ROOTPATH') OR die('No direct access allowed.');
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

class FE_Preview extends FE_Node {
	
	public function __construct($attributes) {
		parent::__construct($attributes);
		$this->_attributes['name'] = '';
	}
	
	protected function _PrepareAttributes_JS() {
		$attributes = Array(
			$this->_FormatAttribute_JS('url', 'sUrl'),
			$this->_FormatAttribute_JS('width', 'iWidth'),
			$this->_FormatAttribute_JS('height', 'iHeight'),
			$this->_FormatAttribute_JS('label', 'sLabel'),
			$this->_FormatDependency_JS()
		);
		return $attributes;
	}
	
	public function Render_Static() {}
	
	public function Populate($value) {}
	
}
