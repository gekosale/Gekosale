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

class Tip extends \FormEngine\Node
{
	
	const UP = 'up';
	const DOWN = 'down';
	
	const EXPANDED = 'expanded';
	const RETRACTED = 'retracted';

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
		$this->_attributes['name'] = '';
		if (isset($this->_attributes['short_tip']) && strlen($this->_attributes['short_tip'])){
			$this->_attributes['retractable'] = true;
		}
	}

	protected function _PrepareAttributes_JS ()
	{
		$attributes = Array(
			$this->_FormatAttribute_JS('tip', 'sTip'),
			$this->_FormatAttribute_JS('direction', 'sDirection'),
			$this->_FormatAttribute_JS('short_tip', 'sShortTip'),
			$this->_FormatAttribute_JS('retractable', 'bRetractable', FE::TYPE_BOOLEAN),
			$this->_FormatAttribute_JS('default_state', 'sDefaultState'),
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
