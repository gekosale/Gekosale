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
 * Class Tax
 *
 * @package Gekosale\Core\Model
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Tax extends Model
{

    protected $table = 'tax';

    public $timestamps = true;

    protected $softDelete = false;

    protected $fillable = ['id', 'value'];

    public function translation()
    {
        return $this->hasMany('Gekosale\Core\Model\TaxTranslation');
    }

    /**
     * Get translations for tax rate
     *
     * @return array
     */
    public function getLanguageData()
    {
        $languageData = [];
        foreach ($this->translation as $translation) {
            $languageData[$translation->language_id] = [
                'name' => $translation->name,
            ];
        }

        return $languageData;
    }
}