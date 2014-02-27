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

use Exception;
use Gekosale\App as App;
use Gekosale\Translation as Translation;

class PriceModifier extends Price
{

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
		if (! isset($this->_attributes['base_price_field']) || ! ($this->_attributes['base_price_field'] instanceof Field)){
			throw new Exception("Base price source field (attribute: base_price_field) not set for field '{$this->_attributes['name']}'.");
		}
		$this->_attributes['base_price_field_name'] = $this->_attributes['base_price_field']->GetName();
		$this->_attributes['suffixes'] = App::getModel('suffix/suffix')->getSuffixTypesForSelect();
	}

	protected function prepareAttributesJs ()
	{
		$attributes = Array(
			$this->formatAttributeJs('name', 'sName'),
			$this->formatAttributeJs('label', 'sLabel'),
			$this->formatAttributeJs('comment', 'sComment'),
			$this->formatAttributeJs('suffix', 'sSuffix'),
			$this->formatAttributeJs('prefixes', 'asPrefixes'),
			$this->formatAttributeJs('error', 'sError'),
			$this->formatAttributeJs('vat_field_name', 'sVatField'),
			$this->formatAttributeJs('base_price_field_name', 'sBasePriceField'),
			$this->formatAttributeJs('vat_values', 'aoVatValues', \FormEngine\FE::TYPE_OBJECT),
			$this->formatAttributeJs('suffixes', 'oSuffixes', \FormEngine\FE::TYPE_OBJECT),
			$this->formatRulesJs(),
			$this->formatDependencyJs(),
			$this->formatDefaultsJs()
		);
		return $attributes;
	}
}
