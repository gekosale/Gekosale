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
     * {@inheritdoc}
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

    /**
     * {@inheritdoc}
     */
    public function registerEventHandlers()
    {
        $this->getXajaxManager()->registerFunctions([
            'getAvailabilityForAjax' => [$this, 'getData'],
            'doDeleteAvailability'   => [$this, 'delete']
        ]);
    }
}