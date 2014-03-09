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

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;

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
    public function init()
    {
        $this->setTableData([
            'id'    => [
                'source' => 'V.id'
            ],
            'name'  => [
                'source' => 'VT.name'
            ],
            'value' => [
                'source' => 'V.value'
            ]
        ]);

        $this->setFrom('
            tax V
            LEFT JOIN tax_translation VT ON VT.tax_id = V.id
        ');

        $this->setGroupBy('
            V.id
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function registerEventHandlers()
    {
        $this->getXajaxManager()->registerFunctions([
            'getTaxForAjax' => [$this, 'getData'],
            'doDeleteTax'   => [$this, 'delete']
        ]);
    }
}