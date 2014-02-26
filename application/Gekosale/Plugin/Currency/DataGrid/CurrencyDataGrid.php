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
namespace Gekosale\Plugin\Currency\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;

/**
 * Class CurrencyDataGrid
 *
 * @package Gekosale\Plugin\Currency\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CurrencyDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * Initializes DataGrid
     */
    public function init()
    {
        $this->options = [
            'id'              => 'currency',
            'appearance'      => [
                'column_select' => false
            ],
            'mechanics'       => [
                'key'           => 'id',
                'rows_per_page' => 25
            ],
            'event_handlers'  => [
                'load'       => [$this, 'getData'],
                'edit_row'   => new DataGridEditAction(),
                'delete_row' => new DataGridDeleteAction(),
                'process'    => new DataGridEventHandler(new ProcessRowEvent()),
                'click_row'  => new DataGridEventHandler(new EditRowEvent()),
            ],
            'row_actions'     => [
                new DataGrid\Action\Edit(),
                new DataGrid\Action\Delete(),
            ],
            'context_actions' => [
                new DataGridEditAction(),
                new DataGridDeleteAction(),
            ]
        ];

        $this->addColumn(new DataGridColumn([
            'id'         => [
                'source' => 'id'
            ],
            'caption'    => $this->trans('Id'),
            'appearance' => [
                'width'   => 90,
                'visible' => false
            ],
            'filter'     => [
                'type' => new DataGrid\Filter\Between()
            ]
        ]));

        $this->addColumn(new DataGridColumn([
            'name'    => ['source' => 'name'],
            'caption' => $this->trans('Name'),
        ]));

        $this->setTableData([
            'id'     => [
                'source' => 'C.id'
            ],
            'name'   => [
                'source' => 'C.name'
            ],
            'symbol' => [
                'source' => 'C.symbol'
            ]
        ]);

        $this->setFrom('
            currency C
        ');

        $this->setGroupBy('
            C.id
        ');
    }

    public function delete($datagrid, $id)
    {
        return $this->deleteRow($datagrid, $id, [$this->repository, 'delete']);
    }
}