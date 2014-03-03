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
 * Class ShopTranslation
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ShopTranslation extends Model implements TranslationModelInterface
{

    protected $table = 'shop_translation';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable = ['shop_id', 'language_id'];

    public function availability()
    {
        return $this->belongsTo('Gekosale\Core\Model\Shop');
    }

    public function language()
    {
        return $this->belongsTo('Gekosale\Core\Model\Language');
    }

    public function scopeHasLanguageId($query, $language)
    {
        return $query->whereLanguageId($language)->first();
    }
}