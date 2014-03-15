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

use Gekosale\Core\Model;

/**
 * Class Company
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Company extends Model
{

    protected $table = 'company';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable = ['id'];

    /**
     * Relation with
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shop()
    {
        return $this->hasMany('Gekosale\Core\Model\Shop');
    }
}