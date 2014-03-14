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
        $this->transaction(function () use ($id) {
            return Shop::destroy($id);
        });
    }

    /**
     * Saves shop
     *
     * @param      $Data
     * @param null $id
     */
    public function save($Data, $id = null)
    {
        $this->transaction(function () use ($Data, $id) {

            $shop = Shop::firstOrNew([
                'id' => $id
            ]);

            $shop->url        = $Data['url'];
            $shop->is_offline = $Data['is_offline'];
            $shop->company_id = $Data['company_id'];
            $shop->save();

            foreach ($this->getLanguageIds() as $language) {

                $translation = ShopTranslation::firstOrNew([
                    'shop_id'     => $shop->id,
                    'language_id' => $language
                ]);

                $translation->setTranslationData($Data, $language);
                $translation->save();
            }
        });
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
        $populateData = [];
        $accessor     = $this->getPropertyAccessor();
        $languageData = $shopData->getTranslationData();

        $accessor->setValue($populateData, '[required_data]', [
            'url'           => $shopData->url,
            'is_offline'    => $shopData->is_offline,
            'company_id'    => $shopData->company_id,
            'language_data' => $languageData
        ]);

        $accessor->setValue($populateData, '[meta_data]', [
            'language_data' => $languageData
        ]);

        return $populateData;
    }
}