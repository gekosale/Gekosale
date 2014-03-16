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
namespace Gekosale\Plugin\Product\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;
use Gekosale\Plugin\Product\Event\ProductDataGridEvent;

/**
 * Class ProductDataGrid
 *
 * @package Gekosale\Plugin\Product\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProductDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->registerEventHandlers();

        $this->addColumn('id', [
            'source' => 'product.id'
        ]);

        $this->addColumn('sku', [
            'source' => 'product.sku'
        ]);

        $this->addColumn('ean', [
            'source' => 'product.ean'
        ]);

        $this->addColumn('sell_price', [
            'source' => 'product.sell_price'
        ]);

        $this->addColumn('sell_price_gross', [
            'source' => 'product.sell_price'
        ]);

        $this->addColumn('stock', [
            'source' => 'product.stock'
        ]);

        $this->addColumn('hierarchy', [
            'source' => 'product.hierarchy'
        ]);

        $this->addColumn('weight', [
            'source' => 'product.weight'
        ]);

        $this->addColumn('name', [
            'source' => 'product_translation.name'
        ]);

        $this->query = $this->getDb()
            ->table('product')
            ->join('product_translation', 'product_translation.product_id', '=', 'product.id')
            ->groupBy('product.id');

        $event = new ProductDataGridEvent($this);

        $this->getDispatcher()->dispatch(ProductDataGridEvent::DATAGRID_INIT_EVENT, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function registerEventHandlers()
    {
        $this->getXajaxManager()->registerFunctions([
            'getProductForAjax' => [$this, 'getData'],
            'doDeleteProduct'   => [$this, 'delete'],
        ]);

        $this->getXajaxManager()->registerFunction(['doUpdateProduct', $this, 'updateProduct']);
    }

    /**
     * Updates product
     *
     * @param $request
     *
     * @return mixed
     */
    public function updateProduct($request)
    {
        return $this->repository->updateProductDataGrid($request);
    }
}