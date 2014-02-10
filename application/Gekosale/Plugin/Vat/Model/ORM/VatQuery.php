<?php

namespace Gekosale\Plugin\Vat\Model\ORM;

use Illuminate\Database\Eloquent\Model;

class VatQuery extends Model
{

    protected $table = 'vat';

    public function translation ()
    {
        return $this->hasMany('Gekosale\Plugin\Vat\Model\ORM\VatI18n');
    }
}