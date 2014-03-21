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
namespace Gekosale\Plugin\PaymentMethod\Repository;

use Gekosale\Core\Repository;
use Gekosale\Core\Model\PaymentMethod;
use Gekosale\Core\Model\PaymentMethodTranslation;

/**
 * Class PaymentMethodRepository
 *
 * @package Gekosale\Plugin\PaymentMethod\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class PaymentMethodRepository extends Repository
{
    /**
     * Returns payment_method collection
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return PaymentMethod::all();
    }

    /**
     * Returns single payment_method model with all shop and deliverer data
     *
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function find($id)
    {
        return PaymentMethod::with('translation', 'shop', 'deliverer')->findOrFail($id);
    }

    /**
     * Deletes payment_method by key or multiple payment_methods if array of ids is passed
     *
     * @param array|int $id
     */
    public function delete($id)
    {
        $this->transaction(function () use ($id) {
            return PaymentMethod::destroy($id);
        });
    }

    /**
     * Saves payment_method model
     *
     * @param array    $Data Submitted form data
     * @param int|null $id   PaymentMethod ID or null if new payment_method
     */
    public function save(array $Data, $id = null)
    {
        $this->transaction(function () use ($Data, $id) {
            $payment_method = PaymentMethod::firstOrNew([
                'id' => $id
            ]);

            $payment_method->enabled          = $Data['enabled'];
            $payment_method->ean              = $Data['ean'];
            $payment_method->sku              = $Data['sku'];
            $payment_method->producer_id      = $Data['producer_id'];
            $payment_method->stock            = $Data['stock'];
            $payment_method->track_stock      = $Data['track_stock'];
            $payment_method->tax_id           = $Data['tax_id'];
            $payment_method->sell_currency_id = $Data['sell_currency_id'];
            $payment_method->buy_currency_id  = $Data['buy_currency_id'];
            $payment_method->buy_price        = $Data['buy_price'];
            $payment_method->sell_price       = $Data['sell_price'];
            $payment_method->weight           = $Data['weight'];
            $payment_method->width            = $Data['width'];
            $payment_method->height           = $Data['height'];
            $payment_method->depth            = $Data['depth'];
            $payment_method->package_size     = $Data['package_size'];
            $payment_method->save();

            foreach ($this->getLanguageIds() as $language) {

                $translation = PaymentMethodTranslation::firstOrNew([
                    'payment_method_id'  => $payment_method->id,
                    'language_id' => $language
                ]);

                $translation->setTranslationData($Data, $language);
                $translation->save();
            }

            $payment_method->sync($payment_method->deliverer(), $Data['deliverers']);
            $payment_method->sync($payment_method->category(), $Data['category']);
            $payment_method->sync($payment_method->shop(), $Data['shops']);
        });
    }

    /**
     * Saves basic payment_method values directly from DataGrid
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
            $payment_method             = $this->find($id);
            $payment_method->ean        = $data['ean'];
            $payment_method->stock      = $data['stock'];
            $payment_method->sell_price = $data['sell_price'];
            $payment_method->hierarchy  = $data['hierarchy'];
            $payment_method->weight     = $data['weight'];
            $payment_method->save();
        });

        return [
            'updated' => true
        ];
    }

    /**
     * Returns array containing values needed to populate the form
     *
     * @param int $id PaymentMethod ID
     *
     * @return array Populate data
     */
    public function getPopulateData($id)
    {
        $payment_methodData  = $this->find($id);
        $populateData = [];
        $accessor     = $this->getPropertyAccessor();
        $languageData = $payment_methodData->getTranslationData();

        $accessor->setValue($populateData, '[basic_pane]', [
            'language_data' => $languageData,
            'enabled'       => $payment_methodData->enabled,
            'ean'           => $payment_methodData->ean,
            'sku'           => $payment_methodData->sku,
            'producer_id'   => $payment_methodData->producer_id,
            'deliverers'    => $payment_methodData->getDeliverers(),
        ]);

        $accessor->setValue($populateData, '[stock_pane]', [
            'stock'       => $payment_methodData->stock,
            'track_stock' => $payment_methodData->track_stock,
        ]);

        $accessor->setValue($populateData, '[category_pane]', [
            'category' => $payment_methodData->getCategories()
        ]);

        $accessor->setValue($populateData, '[description_data]', [
            'language_data' => $languageData
        ]);

        $accessor->setValue($populateData, '[meta_data]', [
            'language_data' => $languageData
        ]);

        $accessor->setValue($populateData, '[price_pane]', [
            'tax_id'           => $payment_methodData->tax_id,
            'sell_currency_id' => $payment_methodData->sell_currency_id,
            'buy_currency_id'  => $payment_methodData->buy_currency_id,
            'buy_price'        => $payment_methodData->buy_price,
            'standard_price'   => [
                'sell_price' => $payment_methodData->sell_price,
            ]
        ]);

        $accessor->setValue($populateData, '[measurements_pane]', [
            'weight'       => $payment_methodData->weight,
            'width'        => $payment_methodData->width,
            'height'       => $payment_methodData->height,
            'depth'        => $payment_methodData->depth,
            'package_size' => $payment_methodData->package_size,

        ]);

        $accessor->setValue($populateData, '[shop_data]', [
            'shops' => $payment_methodData->getShops()
        ]);

        return $populateData;
    }
}