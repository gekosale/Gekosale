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
 * Class Producer
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Producer extends Model
{

    protected $table = 'producer';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable = ['id'];

    /**
     * Relation with producer_translation table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translation()
    {
        return $this->hasMany('Gekosale\Core\Model\ProducerTranslation');
    }

    /**
     * Relation with producer_shop table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shop()
    {
        return $this->hasMany('Gekosale\Core\Model\ProducerShop');
    }

    /**
     * Get translations for producer
     *
     * @return array
     */
    public function getLanguageData()
    {
        $languageData = [];
        foreach ($this->translation as $translation) {
            $languageData[$translation->language_id] = [
                'name'              => $translation->name,
                'short_description' => $translation->short_description,
                'description'       => $translation->description,
                'meta_title'        => $translation->meta_title,
                'meta_keywords'     => $translation->meta_keywords,
                'meta_description'  => $translation->meta_description
            ];
        }

        return $languageData;
    }
}