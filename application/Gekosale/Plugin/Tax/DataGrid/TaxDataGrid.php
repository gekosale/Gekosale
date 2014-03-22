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
namespace Gekosale\Plugin\Tax\DataGrid;

use Gekosale\Core\DataGrid;
use Gekosale\Core\DataGrid\DataGridInterface;
use Gekosale\Plugin\Tax\Event\TaxDataGridEvent;

/**
 * Class TaxDataGrid
 *
 * @package Gekosale\Plugin\Tax\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class TaxDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setOptions([
            'id'             => 'tax',
            'event_handlers' => [
                'load'       => $this->getXajaxManager()->registerFunction(['LoadTax', $this, 'loadData']),
                'edit_row'   => 'editTax',
                'click_row'  => 'editTax',
                'delete_row' => $this->getXajaxManager()->registerFunction(['DeleteTax', $this, 'deleteRow'])
            ],
            'routes'         => [
                'edit' => $this->generateUrl('admin.tax.edit')
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->addColumn('id', [
            'source'     => 'tax.id',
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
            'source'     => 'tax_translation.name',
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
            ->table('tax')
            ->join('tax_translation', 'tax_translation.tax_id', '=', 'tax.id')
            ->groupBy('tax.id');

        $event = new TaxDataGridEvent($this);

        $this->getDispatcher()->dispatch(TaxDataGridEvent::DATAGRID_INIT_EVENT, $event);
    }
}