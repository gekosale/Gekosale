<?php

namespace Gekosale\Core\Model;

class Language extends Eloquent {

	protected $table = 'language';
	public $timestamps = true;
	protected $softDelete = false;
	protected $fillable = array('name');
	protected $visible = array('name');

	public function currency()
	{
		return $this->hasOne('Currency');
	}

}