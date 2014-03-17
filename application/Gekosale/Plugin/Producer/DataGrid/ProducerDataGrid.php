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
namespace Gekosale\Plugin\Producer\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;
use Gekosale\Plugin\Producer\Event\ProducerDataGridEvent;

/**
 * Class ProducerDataGrid
 *
 * @package Gekosale\Plugin\Producer\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProducerDataGrid extends DataGrid implements DataGridInterface
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

        $this->query = $this->getDb()
            ->table('producer')
            ->join('producer_translation', 'producer_translation.producer_id', '=', 'producer.id')
            ->groupBy('producer.id');

        $event = new ProducerDataGridEvent($this);

        $this->getDispatcher()->dispatch(ProducerDataGridEvent::DATAGRID_INIT_EVENT, $event);
    }

    /**
     * Returns route for editAction
     *
     * @return string
     */
    protected function getEditActionRoute()
    {
        return 'admin.producer.edit';
    }
}