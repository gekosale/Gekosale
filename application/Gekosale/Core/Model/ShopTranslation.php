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
 * Class ShopTranslation
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ShopTranslation extends Model
{

    protected $table = 'shop_translation';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable = ['shop_id', 'language_id'];

    protected $translatable
        = [
            'name',
            'meta_title',
            'meta_keywords',
            'meta_description',
        ];

    public function scopeHasLanguageId($query, $language)
    {
        return $query->whereLanguageId($language)->first();
    }
}