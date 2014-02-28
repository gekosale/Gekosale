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
namespace Gekosale\Plugin\Deliverer\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;

/**
 * Class DelivererDataGrid
 *
 * @package Gekosale\Plugin\Deliverer\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class DelivererDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * Initializes DataGrid
     */
    public function init()
    {
        $this->setTableData([
            'id'   => [
                'source' => 'D.id'
            ],
            'name' => [
                'source' => 'DT.name'
            ],
        ]);

        $this->setFrom('
            deliverer D
            LEFT JOIN deliverer_translation DT ON DT.deliverer_id = D.id
        ');

        $this->setGroupBy('
            D.id
        ');
    }

    public function delete($datagrid, $id)
    {
        return $this->deleteRow($datagrid, $id, [$this->repository, 'delete']);
    }
}