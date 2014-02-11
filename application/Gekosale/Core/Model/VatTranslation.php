<?php

namespace Gekosale\Core\Model;

class VatTranslation extends Eloquent {

	protected $table = 'vat_translation';
	public $timestamps = true;
	protected $softDelete = false;
	protected $fillable = array('language_id', 'name');
	protected $visible = array('language_id', 'name');

}