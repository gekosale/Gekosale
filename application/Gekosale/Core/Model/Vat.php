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
 * Class Vat
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Vat extends Eloquent
{

    protected $table = 'vat';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable = array(
        'value'
    );

    public function translation ()
    {
        return $this->hasMany('VatTranslation');
    }
}