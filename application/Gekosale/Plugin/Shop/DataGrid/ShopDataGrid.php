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
namespace Gekosale\Plugin\Shop\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface,
    Gekosale\Core\Model\Shop;

/**
 * Class ShopDataGrid
 *
 * @package Gekosale\Plugin\Shop\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ShopDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->registerEventHandlers();

        $this->addColumn('id', [
            'source' => 'shop.id'
        ]);

        $this->addColumn('name', [
            'source' => 'shop_translation.name'
        ]);

        $this->query = $this->getDb()
            ->table('shop')
            ->join('shop_translation', 'shop_translation.shop_id', '=', 'shop.id')
            ->groupBy('shop.id');
    }

    /**
     * {@inheritdoc}
     */
    public function registerEventHandlers()
    {
        $this->getXajaxManager()->registerFunctions([
            'getShopForAjax' => [$this, 'getData'],
            'doDeleteShop'   => [$this, 'delete']
        ]);
    }
}