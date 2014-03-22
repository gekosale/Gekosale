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
namespace Gekosale\Plugin\Unit\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;
use Gekosale\Plugin\Unit\Event\UnitDataGridEvent;

/**
 * Class UnitDataGrid
 *
 * @package Gekosale\Plugin\Unit\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class UnitDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setOptions([
            'id'             => 'unit',
            'event_handlers' => [
                'load'       => $this->getXajaxManager()->registerFunction(['LoadUnit', $this, 'loadData']),
                'edit_row'   => 'editUnit',
                'click_row'  => 'editUnit',
                'delete_row' => $this->getXajaxManager()->registerFunction(['DeleteUnit', $this, 'deleteRow'])
            ],
            'routes'         => [
                'edit' => $this->generateUrl('admin.unit.edit')
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->addColumn('id', [
            'source'     => 'unit.id',
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
            'source'     => 'unit_translation.name',
            'caption'    => $this->trans('Name'),
            'appearance' => [
                'width' => 70,
                'align' => DataGridInterface::ALIGN_LEFT
            ],
            'filter'     => [
                'type' => DataGridInterface::FILTER_INPUT
            ]
        ]);

        $this->query = $this->getDb()
            ->table('unit')
            ->join('unit_translation', 'unit_translation.unit_id', '=', 'unit.id')
            ->groupBy('unit.id');

        $event = new UnitDataGridEvent($this);

        $this->getDispatcher()->dispatch(UnitDataGridEvent::DATAGRID_INIT_EVENT, $event);
    }
}