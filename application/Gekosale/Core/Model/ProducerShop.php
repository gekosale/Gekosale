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
 * Class ProducerShop
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProducerShop extends Model
{

    protected $table = 'producer_shop';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable = ['producer_id', 'shop_id'];

    /**
     * Relation with producer table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function producer()
    {
        return $this->belongsTo('Gekosale\Core\Model\Producer');
    }

    /**
     * Relation with shop table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('Gekosale\Core\Model\Shop');
    }
}