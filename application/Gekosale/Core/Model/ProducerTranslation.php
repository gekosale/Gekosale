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
 * Class ProducerTranslation
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProducerTranslation extends Model
{

    protected $table = 'producer_translation';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable
        = [
            'producer_id',
            'language_id'
        ];

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
     * Relation with language table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo('Gekosale\Core\Model\Language');
    }
}