<?php

namespace Gekosale\Core\Model;

/**
 * Class Vat
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
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