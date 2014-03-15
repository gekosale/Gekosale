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
namespace Gekosale\Plugin\Product\Repository;

use Gekosale\Core\Repository;
use Gekosale\Core\Model\Product;
use Gekosale\Core\Model\ProductTranslation;

/**
 * Class ProductRepository
 *
 * @package Gekosale\Plugin\Product\Repository
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProductRepository extends Repository
{
    /**
     * Returns product collection
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return Product::all();
    }

    /**
     * Returns single product model with all shop and deliverer data
     *
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function find($id)
    {
        return Product::with('translation', 'shop', 'deliverer')->findOrFail($id);
    }

    /**
     * Deletes product by key or multiple products if array of ids is passed
     *
     * @param array|int $id
     */
    public function delete($id)
    {
        $this->transaction(function () use ($id) {
            return Product::destroy($id);
        });
    }

    /**
     * Saves product model
     *
     * @param array    $Data Submitted form data
     * @param int|null $id   Product ID or null if new product
     */
    public function save(array $Data, $id = null)
    {
        $this->transaction(function () use ($Data, $id) {
            $product = Product::firstOrNew([
                'id' => $id
            ]);

            $product->enabled          = $Data['enabled'];
            $product->ean              = $Data['ean'];
            $product->sku              = $Data['sku'];
            $product->producer_id      = $Data['producer_id'];
            $product->stock            = $Data['stock'];
            $product->track_stock      = $Data['track_stock'];
            $product->tax_id           = $Data['tax_id'];
            $product->sell_currency_id = $Data['sell_currency_id'];
            $product->buy_currency_id  = $Data['buy_currency_id'];
            $product->buy_price        = $Data['buy_price'];
            $product->sell_price       = $Data['sell_price'];
            $product->weight           = $Data['weight'];
            $product->width            = $Data['width'];
            $product->height           = $Data['height'];
            $product->depth            = $Data['depth'];
            $product->package_size     = $Data['package_size'];
            $product->save();

            foreach ($this->getLanguageIds() as $language) {

                $translation = ProductTranslation::firstOrNew([
                    'product_id'  => $product->id,
                    'language_id' => $language
                ]);

                $translation->setTranslationData($Data, $language);
                $translation->save();
            }

            if (!empty($Data['deliverers'])) {
                $product->deliverer()->sync($Data['deliverers']);
            } else {
                $product->deliverer()->detach();
            }

            if (!empty($Data['category'])) {
                $product->category()->sync($Data['category']);
            } else {
                $product->category()->detach();
            }

            if (!empty($Data['shops'])) {
                $product->shop()->sync($Data['shops']);
            } else {
                $product->shop()->detach();
            }
        });
    }

    /**
     * Saves basic product values directly from DataGrid
     *
     * @param array $request
     *
     * @return array
     */
    public function updateProductDataGrid(array $request)
    {
        $id   = $request['id'];
        $data = $request['product'];

        $this->transaction(function () use ($id, $data) {
            $product             = $this->find($id);
            $product->ean        = $data['ean'];
            $product->stock      = $data['stock'];
            $product->sell_price = $data['sell_price'];
            $product->hierarchy  = $data['hierarchy'];
            $product->save();
        });

        return [
            'updated' => true
        ];
    }

    /**
     * Returns array containing values needed to populate the form
     *
     * @param int $id Product ID
     *
     * @return array Populate data
     */
    public function getPopulateData($id)
    {
        $productData  = $this->find($id);
        $populateData = [];
        $accessor     = $this->getPropertyAccessor();
        $languageData = $productData->getTranslationData();

        $accessor->setValue($populateData, '[required_data]', [
            'language_data' => $languageData,
            'deliverers'    => $productData->getDeliverers(),
        ]);

        $accessor->setValue($populateData, '[description_data][language_data]', $languageData);

        $accessor->setValue($populateData, '[meta_data][language_data]', $languageData);

        $accessor->setValue($populateData, '[shop_data][shops]', $productData->getShops());

        return $populateData;
    }
}