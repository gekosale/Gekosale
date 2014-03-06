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

use Illuminate\Database\Eloquent\Model;
/**
 * Class DelivererTranslation
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class DelivererTranslation extends Model
{

    protected $table = 'deliverer_translation';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable = ['deliverer_id', 'language_id', 'name'];

    /**
     * Relation with deliverer table
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliverer()
    {
        return $this->belongsTo('Gekosale\Core\Model\Deliverer');
    }

    /**
     * {@inheritdoc}
     */
    public function language()
    {
        return $this->belongsTo('Gekosale\Core\Model\Language');
    }

    /**
     * {@inheritdoc}
     */
    public function scopeHasLanguageId($query, $language)
    {
        return $query->whereLanguageId($language)->first();
    }
}