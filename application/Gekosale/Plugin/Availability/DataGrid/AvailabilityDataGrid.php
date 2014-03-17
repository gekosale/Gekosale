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
namespace Gekosale\Plugin\Availability\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;
use Gekosale\Plugin\Availability\Event\AvailabilityDataGridEvent;

/**
 * Class AvailabilityDataGrid
 *
 * @package Gekosale\Plugin\Availability\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class AvailabilityDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setOptions([
            'id'             => 'availability',
            'appearance'     => [
                'column_select' => false
            ],
            'mechanics'      => [
                'key' => 'id',
            ],
            'event_handlers' => [
                'load'       => $this->getXajaxManager()->registerFunction(['loadData', $this, 'loadData']),
                'edit_row'   => $this->getXajaxManager()->registerFunction(['editRow', $this, 'editRow']),
                'delete_row' => $this->getXajaxManager()->registerFunction(['deleteRow', $this, 'deleteRow']),
            ],
        ]);

        $this->addColumn('id', [
            'source'     => 'availability.id',
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
            'source'     => 'availability_translation.name',
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
            ->table('availability')
            ->join('availability_translation', 'availability_translation.availability_id', '=', 'availability.id')
            ->groupBy('availability.id');

        $event = new AvailabilityDataGridEvent($this);

        $this->getDispatcher()->dispatch(AvailabilityDataGridEvent::DATAGRID_INIT_EVENT, $event);
    }

    /**
     * Returns route for editAction
     *
     * @return string
     */
    protected function getEditActionRoute()
    {
        return 'admin.availability.edit';
    }
}