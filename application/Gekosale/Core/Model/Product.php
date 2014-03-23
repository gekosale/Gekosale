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
use Gekosale\Core\Helper;

/**
 * Class Product
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Product extends Model implements TranslatableModelInterface
{
    /**
     * @var string
     */
    protected $table = 'product';

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
        return $this->hasMany('Gekosale\Core\Model\ProductTranslation');
    }

    /**
     * Relation with Shop model through pivot table product_shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shop()
    {
        return $this->belongsToMany('Gekosale\Core\Model\Shop', 'product_shop', 'product_id', 'shop_id');
    }

    /**
     * Relation with Category model through pivot table product_category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function category()
    {
        return $this->belongsToMany('Gekosale\Core\Model\Category', 'product_category', 'product_id', 'category_id');
    }

    /**
     * Relation with Deliverer model through pivot table product_deliverer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function deliverer()
    {
        return $this->belongsToMany('Gekosale\Core\Model\Deliverer', 'product_deliverer', 'product_id', 'deliverer_id');
    }

    /**
     * Relation with File model through pivot table product_photo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function photos()
    {
        return $this->belongsToMany('Gekosale\Core\Model\File', 'product_photo', 'product_id', 'file_id');
    }

    /**
     * Fetch shop ids from model
     *
     * @return array
     */
    public function getShops()
    {
        $shops = [];
        foreach ($this->shop as $shop) {
            $shops[] = $shop->id;
        }

        return $shops;
    }

    /**
     * Fetch photo ids from model
     *
     * @return array
     */
    public function getPhotos()
    {
        $photos = [];
        foreach ($this->photos as $photo) {
            $photos[] = $photo->id;
        }

        return $photos;
    }

    /**
     * Fetch category ids from model
     *
     * @return array
     */
    public function getCategories()
    {
        $categories = [];
        foreach ($this->category as $category) {
            $categories[] = $category->id;
        }

        return $categories;
    }

    /**
     * Fetch deliverer ids from model
     *
     * @return array
     */
    public function getDeliverers()
    {
        $deliverers = [];
        foreach ($this->deliverer as $deliverer) {
            $deliverers[] = $deliverer->id;
        }

        return $deliverers;
    }

    /**
     * Mutator for enabled attribute
     *
     * @param $value
     */
    public function setEnabledAttribute($value)
    {
        $this->attributes['enabled'] = (int)$value;
    }

    /**
     * Accessor for enabled attribute
     *
     * @param $value
     *
     * @return int
     */
    public function getEnabledAttribute($value)
    {
        return (int)$value;
    }

    /**
     * Mutator for track_stock attribute
     *
     * @param $value
     */
    public function setTrackStockAttribute($value)
    {
        $this->attributes['track_stock'] = (int)$value;
    }

    /**
     * Accessor for track_stock attribute
     *
     * @param $value
     *
     * @return int
     */
    public function getTrackStockAttribute($value)
    {
        return (int)$value;
    }

    /**
     * Mutator for tax_id attribute
     *
     * @param $value
     */
    public function setTaxIdAttribute($value)
    {
        $this->attributes['tax_id'] = ($value == 0) ? null : (int)$value;
    }

    /**
     * Accessor for tax_id attribute
     *
     * @param $value
     *
     * @return int
     */
    public function getTaxIdAttribute($value)
    {
        return (int)$value;
    }

    /**
     * Mutator for weight attribute
     *
     * @param $value
     */
    public function setWeightAttribute($value)
    {
        $this->attributes['weight'] = Helper::changeCommaToDot($value);
    }

    /**
     * Mutator for sell_price attribute
     *
     * @param $value
     */
    public function setSellPriceAttribute($value)
    {
        $this->attributes['sell_price'] = Helper::changeCommaToDot($value);
    }
}