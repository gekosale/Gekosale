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
 * Class CategoryTranslation
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CategoryTranslation extends Model
{

    /**
     * @var string
     */
    protected $table = 'category_translation';

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
    protected $fillable = ['category_id', 'language_id'];

    /**
     * @var array
     */
    protected $translatable
        = [
            'name',
            'slug',
            'short_description',
            'description',
            'meta_title',
            'meta_keywords',
            'meta_description'
        ];

    public function category()
    {
        return $this->belongsTo('Gekosale\Core\Model\Category');
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