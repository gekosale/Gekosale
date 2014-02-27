<?php

namespace FormEngine\Elements;

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
class TextField extends Field
{
	const SIZE_SHORT = 'short';
	const SIZE_MEDIUM = 'medium';
	const SIZE_LONG = 'long';

	protected function prepareAttributesJs ()
	{
		$attributes = Array(
			$this->formatAttributeJs('name', 'sName'),
			$this->formatAttributeJs('label', 'sLabel'),
			$this->formatAttributeJs('comment', 'sComment'),
			$this->formatAttributeJs('suffix', 'sSuffix'),
			$this->formatAttributeJs('prefix', 'sPrefix'),
			$this->formatAttributeJs('selector', 'sSelector'),
			$this->formatAttributeJs('wrap', 'sWrapClass'),
			$this->formatAttributeJs('class', 'sClass'),
			$this->formatAttributeJs('css_attribute', 'sCssAttribute'),
			$this->formatAttributeJs('max_length', 'iMaxLength'),
			$this->formatAttributeJs('error', 'sError'),
			$this->formatRepeatableJs(),
			$this->formatRulesJs(),
			$this->formatDependencyJs(),
			$this->formatDefaultsJs()
		);
		
		return $attributes;
	}
}
