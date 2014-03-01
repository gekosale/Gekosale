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
namespace Gekosale\Plugin\Shop\Repository;

use Gekosale\Core\Repository,
    Gekosale\Core\Model\Shop,
    Gekosale\Core\Model\ShopTranslation;

/**
 * Class ShopRepository
 *
 * @package Gekosale\Plugin\Shop\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ShopRepository extends Repository
{

    /**
     * Returns a shop collection
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Shop::with('translation', 'company')->get();
    }

    /**
     * Returns the shop model
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($id)
    {
        return Shop::with('translation')->findOrFail($id);
    }

    /**
     * Deletes shop by ID
     *
     * @param $id
     */
    public function delete($id)
    {
        return Shop::destroy($id);
    }

    /**
     * Saves shop
     *
     * @param      $Data
     * @param null $id
     */
    public function save($Data, $id = null)
    {
        $shop = Shop::firstOrNew([
            'id' => $id
        ]);

        $shop->url        = $Data['url'];
        $shop->is_offline = $Data['is_offline'];
        $shop->company_id = $Data['company_id'];
        $shop->save();

        foreach ($Data['name'] as $languageId => $name) {

            $translation = ShopTranslation::firstOrNew([
                'shop_id'     => $shop->id,
                'language_id' => $languageId
            ]);

            $translation->name             = $name;
            $translation->meta_title       = $Data['meta_title'][$languageId];
            $translation->meta_keywords    = $Data['meta_keywords'][$languageId];
            $translation->meta_description = $Data['meta_description'][$languageId];
            $translation->save();
        }
    }

    /**
     * Returns array containing values needed to populate the form
     *
     * @param $id
     *
     * @return array
     */
    public function getPopulateData($id)
    {
        $shopData     = $this->find($id);
        $languageData = $shopData->getLanguageData();

        return [
            'required_data' => [
                'url'           => $shopData->url,
                'is_offline'    => $shopData->is_offline,
                'company_id'    => $shopData->company_id,
                'language_data' => $languageData
            ],
            'meta_data'     => [
                'language_data' => $languageData
            ]
        ];
    }
}