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
			$this->_attributes['vat_field_name'] = $this->_attributes['vat_field']->GetName();
		}
		$this->_attributes['prefixes'] = Array(
			Translation::get('TXT_PRICE_NET'),
			Translation::get('TXT_PRICE_GROSS')
		);
	}

	protected function _PrepareAttributes_JS ()
	{
		$attributes = Array(
			$this->_FormatAttribute_JS('name', 'sName'),
			$this->_FormatAttribute_JS('label', 'sLabel'),
			$this->_FormatAttribute_JS('comment', 'sComment'),
			$this->_FormatAttribute_JS('suffix', 'sSuffix'),
			$this->_FormatAttribute_JS('prefixes', 'asPrefixes'),
			$this->_FormatAttribute_JS('error', 'sError'),
			$this->_FormatAttribute_JS('vat_field_name', 'sVatField'),
			$this->_FormatRules_JS(),
			$this->_FormatDependency_JS(),
			$this->_FormatDefaults_JS()
		);
		return $attributes;
	}

}
