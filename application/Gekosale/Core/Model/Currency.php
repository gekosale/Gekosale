<?php

namespace Gekosale\Core\Model;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{

    protected $table = 'currency';

    public $timestamps = true;

    protected $softDelete = false;
}