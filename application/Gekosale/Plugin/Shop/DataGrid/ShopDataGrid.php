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

use Gekosale\Core\DataGrid;
use Gekosale\Core\DataGrid\DataGridInterface;
use Gekosale\Plugin\Shop\Event\ShopDataGridEvent;

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
        $editEvent = $this->getXajaxManager()->registerFunction(['editRow', $this, 'editRow']);

        $this->setOptions([
            'id'             => 'shop',
            'appearance'     => [
                'column_select' => false
            ],
            'mechanics'      => [
                'key' => 'id',
            ],
            'event_handlers' => [
                'load'       => $this->getXajaxManager()->registerFunction(['loadData', $this, 'loadData']),
                'edit_row'   => $editEvent,
                'click_row'  => $editEvent,
                'delete_row' => $this->getXajaxManager()->registerFunction(['deleteRow', $this, 'deleteRow']),
                'update_row' => $this->getXajaxManager()->registerFunction(['updateRow', $this, 'updateRow']),
            ],
        ]);

        $this->addColumn('id', [
            'source'     => 'shop.id',
            'caption'    => $this->trans('Id'),
            'sorting'    => [
                'default_order' => DataGridInterface::SORT_DIR_DESC
            ],
            'appearance' => [
                'width'   => 90,
                'visible' => false
            ],
            'filter'     => [
                'type' => DataGridInterface::FILTER_BETWEEN
            ]
        ]);

        $this->addColumn('name', [
            'source'     => 'shop_translation.name',
            'caption'    => $this->trans('Name'),
            'appearance' => [
                'width' => 570,
                'align' => DataGridInterface::ALIGN_LEFT
            ],
            'filter'     => [
                'type' => DataGridInterface::FILTER_INPUT
            ]
        ]);

        $this->addColumn('is_offline', [
            'source'     => 'shop.is_offline',
            'caption'    => $this->trans('Offline mode'),
            'selectable' => true,
            'appearance' => [
                'width' => 70,
                'align' => DataGridInterface::ALIGN_LEFT
            ],
            'filter'     => [
                'type'    => DataGridInterface::FILTER_SELECT,
                'options' => $this->getOfflineFilterOptions()
            ]
        ]);

        $this->query = $this->getDb()
            ->table('shop')
            ->join('shop_translation', 'shop_translation.shop_id', '=', 'shop.id')
            ->groupBy('shop.id');

        $event = new ShopDataGridEvent($this);

        $this->getDispatcher()->dispatch(ShopDataGridEvent::DATAGRID_INIT_EVENT, $event);
    }

    protected function getOfflineFilterOptions()
    {
        $options   = [];
        $options[] = [
            'id'      => 0,
            'caption' => $this->trans('Offline'),
        ];
        $options[] = [
            'id'      => 1,
            'caption' => $this->trans('Online'),
        ];

        return $options;
    }

    /**
     * Returns route for editAction
     *
     * @return string
     */
    protected function getEditActionRoute()
    {
        return 'admin.shop.edit';
    }
}