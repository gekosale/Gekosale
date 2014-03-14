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
 * Class Shop
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Shop extends Model implements TranslatableModelInterface
{

    protected $table = 'shop';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable = ['id'];

    public function translation()
    {
        return $this->hasMany('Gekosale\Core\Model\ShopTranslation');
    }

    public function company()
    {
        return $this->belongsTo('Gekosale\Core\Model\Company');
    }

    /**
     * Mutator for is_offline attribute
     *
     * @param $value
     */
    public function setIsOfflineAttribute($value)
    {
        $this->attributes['is_offline'] = (int)$value;
    }

    /**
     * Accessor for is_offline attribute
     *
     * @param $value
     *
     * @return int
     */
    public function getIsOfflineAttribute($value)
    {
        return (int)$value;
    }
}