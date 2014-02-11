<?php

namespace Gekosale\Core\Model;

class Vat extends Eloquent {

	protected $table = 'vat';
	public $timestamps = true;
	protected $softDelete = false;
	protected $fillable = array('value');

	public function translation()
	{
		return $this->hasMany('VatTranslation');
	}

}