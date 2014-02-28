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

/**
 * Class AvailabilityDataGrid
 *
 * @package Gekosale\Plugin\Availability\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class AvailabilityDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * Initializes DataGrid
     */
    public function init()
    {
        $this->setTableData([
            'id'   => [
                'source' => 'A.id'
            ],
            'name' => [
                'source' => 'AT.name'
            ],
        ]);

        $this->setFrom('
            availability A
            LEFT JOIN availability_translation AT ON AT.availability_id = A.id
        ');

        $this->setGroupBy('
            A.id
        ');
    }

    public function delete($datagrid, $id)
    {
        return $this->deleteRow($datagrid, $id, [$this->repository, 'delete']);
    }
}