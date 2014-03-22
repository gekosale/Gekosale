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
namespace Gekosale\Plugin\File\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;
use Gekosale\Plugin\File\Event\FileDataGridEvent;

/**
 * Class FileDataGrid
 *
 * @package Gekosale\Plugin\File\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class FileDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $editEvent = $this->getXajaxManager()->registerFunction(['editRow', $this, 'editRow']);

        $this->setOptions([
            'id'             => 'file',
            'event_handlers' => [
                'load'       => $this->getXajaxManager()->registerFunction(['LoadFiles', $this, 'loadData']),
                'edit_row'   => $editEvent,
                'click_row'  => $editEvent,
                'delete_row' => $this->getXajaxManager()->registerFunction(['DeleteFile', $this, 'deleteRow']),
                'update_row' => false,
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->addColumn('id', [
            'source'     => 'file.id',
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
            'source'     => 'file.name',
            'caption'    => $this->trans('Name'),
            'sorting'    => [
                'default_order' => DataGridInterface::SORT_DIR_DESC
            ],
            'appearance' => [
                'width' => 190,
            ],
            'filter'     => [
                'type' => DataGridInterface::FILTER_BETWEEN
            ]
        ]);

        $this->addColumn('extension', [
            'source'     => 'file.extension',
            'caption'    => $this->trans('Extension'),
            'sorting'    => [
                'default_order' => DataGridInterface::SORT_DIR_DESC
            ],
            'appearance' => [
                'width' => 90,
            ],
            'filter'     => [
                'type' => DataGridInterface::FILTER_INPUT
            ]
        ]);

        $this->addColumn('preview', [
            'source'           => 'file.id',
            'caption'          => $this->trans('Thumb'),
            'sorting'          => [
                'default_order' => DataGridInterface::SORT_DIR_DESC
            ],
            'appearance'       => [
                'width' => 90,
            ],
            'process_function' => [$this, 'getPreview']
        ]);

        $this->query = $this->getDb()
            ->table('file')
            ->groupBy('file.id');

        $event = new FileDataGridEvent($this);

        $this->getDispatcher()->dispatch(FileDataGridEvent::DATAGRID_INIT_EVENT, $event);
    }

    protected function getPreview($id)
    {
        return 'http://gekosale3.tpl/design/_images_panel/datagrid/clear-selection.png';
    }
}