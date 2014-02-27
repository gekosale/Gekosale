<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace Gekosale\Core\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Currency
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Currency extends Model
{

    protected $table = 'currency';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable = array(
        'id'
    );

    public function toSelect(){

    }
}