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

class RightsTable extends Field
{

	public function Populate ($value)
	{
		if (is_array($this->_value)){
			foreach ($this->_value as $c => &$cV){
				foreach ($cV as $a => &$aV){
					if (isset($value[$c][$a]) && $value[$c][$a]){
						$aV = 1;
					}
					else{
						$aV = 0;
					}
				}
			}
		}
		else{
			$this->_value = Array();
		}
		if (is_array($value)){
			foreach ($value as $c => $cV2){
				if (! isset($this->_value[$c])){
					$this->_value[$c] = $cV2;
				}
			}
		}
	}

	protected function prepareAttributesJs ()
	{
		$attributes = Array(
			$this->formatAttributeJs('name', 'sName'),
			$this->formatAttributeJs('label', 'sLabel'),
			$this->formatAttributeJs('comment', 'sComment'),
			$this->formatAttributeJs('error', 'sError'),
			$this->formatAttributeJs('controllers', 'asControllers', FE::TYPE_OBJECT),
			$this->formatAttributeJs('actions', 'asActions', FE::TYPE_OBJECT),
			$this->formatRulesJs(),
			$this->formatDependencyJs(),
			$this->formatDefaultsJs()
		);
		return $attributes;
	}

	protected function formatDefaultsJs ()
	{
		$values = $this->GetValue();
		if (empty($values)){
			return '';
		}
		return 'aabDefaults: ' . json_encode($values);
	}

}
