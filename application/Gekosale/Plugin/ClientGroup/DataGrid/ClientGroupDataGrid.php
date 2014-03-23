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
namespace Gekosale\Plugin\ClientGroup\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;
use Gekosale\Plugin\ClientGroup\Event\ClientGroupDataGridEvent;

/**
 * Class ClientGroupDataGrid
 *
 * @package Gekosale\Plugin\ClientGroup\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ClientGroupDataGrid extends DataGrid implements DataGridInterface
{
    public function configure()
    {
        $this->setOptions([
            'id'             => 'client_group',
            'event_handlers' => [
                'load'       => $this->getXajaxManager()->registerFunction(['LoadClientGroup', $this, 'loadData']),
                'edit_row'   => 'editClientGroup',
                'click_row'  => 'editClientGroup',
                'delete_row' => $this->getXajaxManager()->registerFunction(['DeleteClientGroup', $this, 'deleteRow']),
            ],
            'routes'         => [
                'edit' => $this->generateUrl('admin.client_group.edit')
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->addColumn('id', [
            'source'     => 'client_group.id',
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
            'source'     => 'client_group_translation.name',
            'caption'    => $this->trans('Name'),
            'appearance' => [
                'width' => 600,
                'align' => DataGridInterface::ALIGN_LEFT
            ],
            'filter'     => [
                'type' => DataGridInterface::FILTER_INPUT
            ]
        ]);

        $this->addColumn('discount', [
            'source'     => 'client_group.discount',
            'caption'    => $this->trans('Discount'),
            'appearance' => [
                'width' => 40,
                'align' => DataGridInterface::ALIGN_LEFT
            ],
            'filter'     => [
                'type' => DataGridInterface::FILTER_BETWEEN
            ]
        ]);

        $this->query = $this->getDb()
            ->table('client_group')
            ->join('client_group_translation', 'client_group_translation.client_group_id', '=', 'client_group.id')
            ->groupBy('client_group.id');

        $event = new ClientGroupDataGridEvent($this);

        $this->getDispatcher()->dispatch(ClientGroupDataGridEvent::DATAGRID_INIT_EVENT, $event);
    }
}