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
    public function configure()
    {
        $this->setOptions([
            'id'             => 'producer',
            'event_handlers' => [
                'load'       => $this->getXajaxManager()->registerFunction(['LoadProducer', $this, 'loadData']),
                'edit_row'   => 'editProducer',
                'click_row'  => 'editProducer',
                'delete_row' => $this->getXajaxManager()->registerFunction(['DeleteProducer', $this, 'deleteRow']),
            ],
            'routes'         => [
                'edit'  => $this->generateUrl('admin.producer.edit')
            ]
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->addColumn('id', [
            'source'     => 'producer.id',
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
            'source'     => 'producer_translation.name',
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
}