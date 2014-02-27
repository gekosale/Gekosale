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
namespace Gekosale\Plugin\Vat\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;

/**
 * Class VatDataGrid
 *
 * @package Gekosale\Plugin\Vat\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class VatDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * Initializes DataGrid
     */
    public function init()
    {
        $this->setTableData([
            'id'     => [
                'source' => 'V.id'
            ],
            'name'   => [
                'source' => 'VT.name'
            ],
            'value' => [
                'source' => 'V.value'
            ]
        ]);

        $this->setFrom('
            vat V
            LEFT JOIN vat_translation VT ON VT.vat_id = V.id
        ');

        $this->setGroupBy('
            V.id
        ');
    }

    public function delete($datagrid, $id)
    {
        return $this->deleteRow($datagrid, $id, [$this->repository, 'delete']);
    }
}