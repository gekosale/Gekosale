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

class RangeEditor extends OptionedField
{

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
		if (isset($this->_attributes['allow_vat']) && ! $this->_attributes['allow_vat']){
			$this->_attributes['vat_values'] = Array();
		}
		else{
			$this->_attributes['vat_values'] = App::getModel('vat/vat')->getVATAllForRangeEditor();
		}
		if (! isset($this->_attributes['range_precision'])){
			$this->_attributes['range_precision'] = 2;
		}
		if (! isset($this->_attributes['price_precision'])){
			$this->_attributes['price_precision'] = 2;
		}
		$this->_attributes['prefixes'] = Array(
			$this->trans('TXT_PRICE_NET'),
			$this->trans('TXT_PRICE_GROSS')
		);
	}

	protected function _PrepareAttributes_JS ()
	{
		$attributes = Array(
			$this->_FormatAttribute_JS('name', 'sName'),
			$this->_FormatAttribute_JS('label', 'sLabel'),
			$this->_FormatAttribute_JS('comment', 'sComment'),
			$this->_FormatAttribute_JS('suffix', 'sSuffix'),
			$this->_FormatAttribute_JS('price_precision', 'iPricePrecision'),
			$this->_FormatAttribute_JS('range_precision', 'iRangePrecision'),
			$this->_FormatAttribute_JS('range_suffix', 'sRangeSuffix'),
			$this->_FormatAttribute_JS('prefixes', 'asPrefixes'),
			$this->_FormatAttribute_JS('allow_vat', 'bAllowVat', FE::TYPE_BOOLEAN),
			$this->_FormatAttribute_JS('error', 'sError'),
			$this->_FormatAttribute_JS('vat_values', 'aoVatValues', FE::TYPE_OBJECT),
			$this->_FormatOptions_JS(),
			$this->_FormatRules_JS(),
			$this->_FormatDependency_JS(),
			$this->_FormatDefaults_JS()
		);
		return $attributes;
	}

}
