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
 * Class Producer
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Producer extends Model implements TranslatableModelInterface
{
    /**
     * @var string
     */
    protected $table = 'producer';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var bool
     */
    protected $softDelete = false;

    /**
     * @var array
     */
    protected $fillable = ['id'];

    /**
     * {@inheritdoc}
     */
    public function translation()
    {
        return $this->hasMany('Gekosale\Core\Model\ProducerTranslation');
    }

    /**
     * Relation with Shop model through pivot table producer_shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shop()
    {
        return $this->belongsToMany('Gekosale\Core\Model\Shop', 'producer_shop', 'producer_id', 'shop_id');
    }

    /**
     * Relation with Deliverer model through pivot table producer_deliverer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function deliverer()
    {
        return $this->belongsToMany('Gekosale\Core\Model\Deliverer', 'producer_deliverer', 'producer_id', 'deliverer_id');
    }
}