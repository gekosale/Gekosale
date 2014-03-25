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
    public function configure()
    {
        $this->setOptions([
            'id'             => 'shop',
            'event_handlers' => [
                'load'       => $this->getXajaxManager()->registerFunction(['LoadShop', $this, 'loadData']),
                'edit_row'   => 'editShop',
                'click_row'  => 'editShop',
                'delete_row' => $this->getXajaxManager()->registerFunction(['DeleteShop', $this, 'deleteRow']),
                'update_row' => $this->getXajaxManager()->registerFunction(['UpdateShop', $this, 'updateRow'])
            ],
            'routes'         => [
                'edit' => $this->generateUrl('admin.shop.edit')
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
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

        $this->addColumn('layout_theme_id', [
            'source'     => 'shop.layout_theme_id',
            'caption'    => $this->trans('Theme'),
            'selectable' => true,
            'appearance' => [
                'width' => 70,
                'align' => DataGridInterface::ALIGN_LEFT
            ],
            'filter'     => [
                'type'    => DataGridInterface::FILTER_SELECT,
                'options' => $this->getLayoutThemeFilterOptions()
            ]
        ]);

        $this->addColumn('offline', [
            'source'     => 'shop.offline',
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

    protected function getLayoutThemeFilterOptions()
    {
        $themes  = $this->get('layout_theme.repository')->getAllLayoutThemeToSelect();
        $options = [];

        foreach ($themes as $id => $name) {
            $options[] = [
                'id'      => $id,
                'caption' => $name
            ];
        }

        return $options;
    }
}