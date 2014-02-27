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
use Gekosale\Translation as Translation;
use FormEngine\FE as FE;

class Price extends TextField
{

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
		if (isset($this->_attributes['vat_field']) && is_subclass_of($this->_attributes['vat_field'], 'FormEngine\Elements\Field')){
			$this->_attributes['vat_field_name'] = $this->_attributes['vat_field']->getName();
		}
		$this->_attributes['prefixes'] = Array(
			Translation::get('TXT_PRICE_NET'),
			Translation::get('TXT_PRICE_GROSS')
		);
	}

	protected function prepareAttributesJavascript ()
	{
		$attributes = Array(
			$this->formatAttributeJavascript('name', 'sName'),
			$this->formatAttributeJavascript('label', 'sLabel'),
			$this->formatAttributeJavascript('comment', 'sComment'),
			$this->formatAttributeJavascript('suffix', 'sSuffix'),
			$this->formatAttributeJavascript('prefixes', 'asPrefixes'),
			$this->formatAttributeJavascript('error', 'sError'),
			$this->formatAttributeJavascript('vat_field_name', 'sVatField'),
			$this->formatRulesJavascript(),
			$this->formatDependencyJavascript(),
			$this->formatDefaultsJavascript()
		);
		return $attributes;
	}

}
