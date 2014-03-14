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
 * Class ProductTranslation
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProductTranslation extends Model
{

    /**
     * @var string
     */
    protected $table = 'product_translation';

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
    protected $fillable
        = [
            'product_id',
            'language_id'
        ];

    /**
     * @var array
     */
    protected $translatable
        = [
            'name',
            'slug',
            'short_description',
            'description',
            'long_description',
            'meta_title',
            'meta_keywords',
            'meta_description'
        ];

    /**
     * Relation with product table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('Gekosale\Core\Model\Product');
    }

    /**
     * Relation with language table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo('Gekosale\Core\Model\Language');
    }
}