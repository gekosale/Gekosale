<?php

namespace Gekosale\Plugin\Company\Model\ORM;

use Illuminate\Database\Eloquent\Model;

class CompanyQuery extends Model
{

    protected $table = 'company';

    protected $fillable = array(
        'id',
    );
}