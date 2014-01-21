<?php

namespace SimpleForm\Elements;

class TextArea extends \SimpleForm\Element
{
	public $attributes;

	public function __construct ($attributes)
	{
		if (! isset($attributes['default_value'])){
			$attributes['value'] = '';
		}
		
		if (! isset($attributes['rules'])){
			$attributes['rules'] = Array();
		}
		
		$this->attributes = $attributes;
	}
}
