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
namespace Gekosale\Plugin\ShippingMethod\Repository;

use Gekosale\Core\Repository;
use Gekosale\Core\Model\ShippingMethod;
use Gekosale\Core\Model\ShippingMethodTranslation;

/**
 * Class ShippingMethodRepository
 *
 * @package Gekosale\Plugin\ShippingMethod\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ShippingMethodRepository extends Repository
{
    /**
     * Returns shipping_method collection
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return ShippingMethod::all();
    }

    /**
     * Returns single shipping_method model with all shop and deliverer data
     *
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function find($id)
    {
        return ShippingMethod::with('translation', 'shop', 'deliverer')->findOrFail($id);
    }

    /**
     * Deletes shipping_method by key or multiple shipping_methods if array of ids is passed
     *
     * @param array|int $id
     */
    public function delete($id)
    {
        $this->transaction(function () use ($id) {
            return ShippingMethod::destroy($id);
        });
    }

    /**
     * Saves shipping_method model
     *
     * @param array    $Data Submitted form data
     * @param int|null $id   ShippingMethod ID or null if new shipping_method
     */
    public function save(array $Data, $id = null)
    {
        $this->transaction(function () use ($Data, $id) {
            $shipping_method = ShippingMethod::firstOrNew([
                'id' => $id
            ]);

            $shipping_method->enabled          = $Data['enabled'];
            $shipping_method->ean              = $Data['ean'];
            $shipping_method->sku              = $Data['sku'];
            $shipping_method->producer_id      = $Data['producer_id'];
            $shipping_method->stock            = $Data['stock'];
            $shipping_method->track_stock      = $Data['track_stock'];
            $shipping_method->tax_id           = $Data['tax_id'];
            $shipping_method->sell_currency_id = $Data['sell_currency_id'];
            $shipping_method->buy_currency_id  = $Data['buy_currency_id'];
            $shipping_method->buy_price        = $Data['buy_price'];
            $shipping_method->sell_price       = $Data['sell_price'];
            $shipping_method->weight           = $Data['weight'];
            $shipping_method->width            = $Data['width'];
            $shipping_method->height           = $Data['height'];
            $shipping_method->depth            = $Data['depth'];
            $shipping_method->package_size     = $Data['package_size'];
            $shipping_method->save();

            foreach ($this->getLanguageIds() as $language) {

                $translation = ShippingMethodTranslation::firstOrNew([
                    'shipping_method_id'  => $shipping_method->id,
                    'language_id' => $language
                ]);

                $translation->setTranslationData($Data, $language);
                $translation->save();
            }

            $shipping_method->sync($shipping_method->deliverer(), $Data['deliverers']);
            $shipping_method->sync($shipping_method->category(), $Data['category']);
            $shipping_method->sync($shipping_method->shop(), $Data['shops']);
        });
    }

    /**
     * Saves basic shipping_method values directly from DataGrid
     *
     * @param array $request
     *
     * @return array
     */
    public function updateDataGridRow($request)
    {
        $id   = $request['id'];
        $data = $request['data'];

        $this->transaction(function () use ($id, $data) {
            $shipping_method             = $this->find($id);
            $shipping_method->ean        = $data['ean'];
            $shipping_method->stock      = $data['stock'];
            $shipping_method->sell_price = $data['sell_price'];
            $shipping_method->hierarchy  = $data['hierarchy'];
            $shipping_method->weight     = $data['weight'];
            $shipping_method->save();
        });

        return [
            'updated' => true
        ];
    }

    /**
     * Returns array containing values needed to populate the form
     *
     * @param int $id ShippingMethod ID
     *
     * @return array Populate data
     */
    public function getPopulateData($id)
    {
        $shipping_methodData  = $this->find($id);
        $populateData = [];
        $accessor     = $this->getPropertyAccessor();
        $languageData = $shipping_methodData->getTranslationData();

        $accessor->setValue($populateData, '[basic_pane]', [
            'language_data' => $languageData,
            'enabled'       => $shipping_methodData->enabled,
            'ean'           => $shipping_methodData->ean,
            'sku'           => $shipping_methodData->sku,
            'producer_id'   => $shipping_methodData->producer_id,
            'deliverers'    => $shipping_methodData->getDeliverers(),
        ]);

        $accessor->setValue($populateData, '[stock_pane]', [
            'stock'       => $shipping_methodData->stock,
            'track_stock' => $shipping_methodData->track_stock,
        ]);

        $accessor->setValue($populateData, '[category_pane]', [
            'category' => $shipping_methodData->getCategories()
        ]);

        $accessor->setValue($populateData, '[description_data]', [
            'language_data' => $languageData
        ]);

        $accessor->setValue($populateData, '[meta_data]', [
            'language_data' => $languageData
        ]);

        $accessor->setValue($populateData, '[price_pane]', [
            'tax_id'           => $shipping_methodData->tax_id,
            'sell_currency_id' => $shipping_methodData->sell_currency_id,
            'buy_currency_id'  => $shipping_methodData->buy_currency_id,
            'buy_price'        => $shipping_methodData->buy_price,
            'standard_price'   => [
                'sell_price' => $shipping_methodData->sell_price,
            ]
        ]);

        $accessor->setValue($populateData, '[measurements_pane]', [
            'weight'       => $shipping_methodData->weight,
            'width'        => $shipping_methodData->width,
            'height'       => $shipping_methodData->height,
            'depth'        => $shipping_methodData->depth,
            'package_size' => $shipping_methodData->package_size,

        ]);

        $accessor->setValue($populateData, '[shop_data]', [
            'shops' => $shipping_methodData->getShops()
        ]);

        return $populateData;
    }
}