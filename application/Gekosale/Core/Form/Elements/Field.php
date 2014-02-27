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

class Field extends \FormEngine\Node
{
	
	protected $_value;
	protected $_globalvalue;

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
		$this->_value = '';
		$this->_globalvalue = '';
		if (isset($this->_attributes['default'])){
			$this->Populate($attributes['default']);
		}
	}

	public function Validate ($values = Array())
	{
		if (! isset($this->_attributes['rules']) or ! is_array($this->_attributes['rules'])){
			return true;
		}
		$result = true;
		foreach ($this->_attributes['rules'] as $rule){
			if (isset($this->_value) and is_array($this->_value)){
				foreach ($this->_value as $i => $value){
					$skip = false;
					if (isset($this->_attributes['dependencies']) and is_array($this->_attributes['dependencies'])){
						foreach ($this->_attributes['dependencies'] as $dependency){
							if ((($dependency->type == \FormEngine\Dependency::HIDE) and $dependency->Evaluate($value, $i)) or (($dependency->type == \FormEngine\Dependency::SHOW) and ! $dependency->Evaluate($value, $i)) or (($dependency->type == \FormEngine\Dependency::IGNORE) and $dependency->Evaluate($value, $i))){
								$skip = true;
								break;
							}
						}
					}
					if (! $skip){
						if ($rule instanceof FE_RuleLanguageUnique){
							$rule->setLanguage($i);
						}
						if (($checkResult = $rule->Check($value)) !== true){
							if (! isset($this->_attributes['error']) or ! is_array($this->_attributes['error'])){
								$this->_attributes['error'] = ($i > 0) ? array_fill(0, $i, '') : Array();
							}
							elseif ($i > 0){
								$this->_attributes['error'] = $this->_attributes['error'] + array_fill(0, $i, '');
							}
							$this->_attributes['error'][$i] = $checkResult;
							$result = false;
						}
					}
				}
			}
			else{
				if (isset($this->_attributes['dependencies']) and is_array($this->_attributes['dependencies'])){
					foreach ($this->_attributes['dependencies'] as $dependency){
						if ((($dependency->type == \FormEngine\Dependency::HIDE) and $dependency->Evaluate($this->_value)) or (($dependency->type == \FormEngine\Dependency::SHOW) and ! $dependency->Evaluate($this->_value)) or (($dependency->type == \FormEngine\Dependency::IGNORE) and $dependency->Evaluate($this->_value))){
							return $result;
						}
					}
				}
				if (($checkResult = $rule->Check($this->_value)) !== true){
					$this->_attributes['error'] = $checkResult;
					$result = false;
				}
			}
		}
		return $result;
	}

	public function Populate ($value)
	{
		$value = $this->_Filter($value);
		$this->_value = $value;
	}

	public function PopulateGlobal ($globalvalue)
	{
		$globalvalue = $this->_Filter($globalvalue);
		$this->_globalvalue = $globalvalue;
	}

	public function GetValue ()
	{
		if (! isset($this->_value)){
			return null;
		}
		return $this->_value;
	}

	protected function _FormatDefaults_JS ()
	{
		$values = $this->GetValue();
		if (empty($values)){
			return '';
		}
		if (is_array($values)){
			return 'asDefaults: ' . json_encode($values);
		}
		else{
			return 'sDefault: ' . json_encode($values);
		}
	}

	protected function _FormatRules_JS ()
	{
		if (! isset($this->_attributes['rules']) or ! is_array($this->_attributes['rules'])){
			return '';
		}
		$rules = Array();
		foreach ($this->_attributes['rules'] as $rule){
			$rules[] = $rule->Render();
		}
		return 'aoRules: [' . implode(', ', $rules) . ']';
	}

}
