<?php

namespace SimpleForm\Elements;

class Radio extends \SimpleForm\Element
{
	public $attributes;

	public function __construct ($attributes)
	{
		if (! isset($attributes['rules'])){
			$attributes['rules'] = Array();
		}
		
		$this->attributes = $attributes;
	}
}
