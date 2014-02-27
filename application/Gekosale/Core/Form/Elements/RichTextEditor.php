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
use FormEngine\FE as FE;

class RichTextEditor extends Textarea
{

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
		$this->_attributes['advanced'] = true;
	}

	protected function prepareAttributesJavascript ()
	{
		$attributes = Array(
			$this->formatAttributeJavascript('name', 'sName'),
			$this->formatAttributeJavascript('label', 'sLabel'),
			$this->formatAttributeJavascript('rows', 'iRows', FE::TYPE_NUMBER),
			$this->formatAttributeJavascript('cols', 'iCols', FE::TYPE_NUMBER),
			$this->formatAttributeJavascript('comment', 'sComment'),
			$this->formatAttributeJavascript('error', 'sError'),
			$this->formatAttributeJavascript('advanced', 'bAdvanced'),
			$this->formatAttributeJavascript('language', 'sLanguage'),
			$this->formatRepeatableJavascript(),
			$this->formatRulesJavascript(),
			$this->formatDependencyJavascript(),
			$this->formatDefaultsJavascript()
		);
		return $attributes;
	}

}
