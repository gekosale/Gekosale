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

/**
 * Class Language
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Language extends Eloquent
{

    protected $table = 'language';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable = array(
        'name'
    );

    protected $visible = array(
        'name'
    );

    public function currency ()
    {
        return $this->hasOne('Currency');
    }
}