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
    Gekosale\Core\DataGrid\DataGridInterface;

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
        $this->setTableData([
            'id'   => [
                'source' => 'S.id'
            ],
            'name' => [
                'source' => 'ST.name'
            ],
        ]);

        $this->setFrom('
            shop S
            LEFT JOIN shop_translation ST ON ST.shop_id = S.id
        ');

        $this->setGroupBy('
            S.id
        ');
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