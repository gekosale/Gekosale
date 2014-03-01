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
namespace Gekosale\Plugin\Unit\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;

/**
 * Class UnitDataGrid
 *
 * @package Gekosale\Plugin\Unit\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class UnitDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setTableData([
            'id'   => [
                'source' => 'U.id'
            ],
            'name' => [
                'source' => 'UT.name'
            ],
        ]);

        $this->setFrom('
            unit U
            LEFT JOIN unit_translation UT ON UT.unit_id = U.id
        ');

        $this->setGroupBy('
            U.id
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function registerEventHandlers()
    {
        $this->getXajaxManager()->registerFunctions([
            'getUnitForAjax' => [$this, 'getData'],
            'doDeleteUnit'   => [$this, 'delete']
        ]);
    }
}