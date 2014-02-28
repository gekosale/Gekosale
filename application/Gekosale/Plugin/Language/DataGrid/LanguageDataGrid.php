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
namespace Gekosale\Plugin\Language\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;

/**
 * Class LanguageDataGrid
 *
 * @package Gekosale\Plugin\Language\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class LanguageDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * Initializes DataGrid
     */
    public function init()
    {
        $this->setTableData([
            'id'     => [
                'source' => 'L.id'
            ],
            'name'   => [
                'source' => 'L.name'
            ],
            'locale'   => [
                'source' => 'L.locale'
            ]
        ]);

        $this->setFrom('
            language L
        ');

        $this->setGroupBy('
            L.id
        ');
    }

    public function delete($datagrid, $id)
    {
        return $this->deleteRow($datagrid, $id, [$this->repository, 'delete']);
    }
}