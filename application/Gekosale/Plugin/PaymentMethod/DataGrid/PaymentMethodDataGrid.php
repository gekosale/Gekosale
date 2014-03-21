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
namespace Gekosale\Plugin\PaymentMethod\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;
use Gekosale\Plugin\PaymentMethod\Event\PaymentMethodDataGridEvent;

/**
 * Class PaymentMethodDataGrid
 *
 * @package Gekosale\Plugin\PaymentMethod\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class PaymentMethodDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $editEvent = $this->getXajaxManager()->registerFunction(['editRow', $this, 'editRow']);

        $this->setOptions([
            'id'             => 'payment_method',
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
            'source'     => 'payment_method.id',
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
            'source'     => 'payment_method_translation.name',
            'caption'    => $this->trans('Name'),
            'appearance' => [
                'width' => 70,
                'align' => DataGridInterface::ALIGN_LEFT
            ],
            'filter'     => [
                'type' => DataGridInterface::FILTER_INPUT
            ]
        ]);

        $this->addColumn('enabled', [
            'source'     => 'payment_method.enabled',
            'caption'    => $this->trans('Enabled'),
            'selectable' => true,
            'appearance' => [
                'width' => 20,
            ],
            'filter'     => [
                'type'    => DataGridInterface::FILTER_SELECT,
                'options' => $this->getEnabledFilterOptions()
            ]
        ]);

        $this->addColumn('hierarchy', [
            'source'     => 'payment_method.hierarchy',
            'caption'    => $this->trans('Hierarchy'),
            'editable'   => true,
            'appearance' => [
                'width' => 40,
            ],
            'filter'     => [
                'type' => DataGridInterface::FILTER_BETWEEN
            ]
        ]);

        $this->query = $this->getDb()
            ->table('payment_method')
            ->join('payment_method_translation', 'payment_method_translation.payment_method_id', '=', 'payment_method.id')
            ->groupBy('payment_method.id');

        $event = new PaymentMethodDataGridEvent($this);

        $this->getDispatcher()->dispatch(PaymentMethodDataGridEvent::DATAGRID_INIT_EVENT, $event);
    }

    protected function getEnabledFilterOptions()
    {
        $options   = [];
        $options[] = [
            'id'      => 0,
            'caption' => $this->trans('Yes'),
        ];
        $options[] = [
            'id'      => 1,
            'caption' => $this->trans('No'),
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
        return 'admin.payment_method.edit';
    }
}