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
namespace Gekosale\Plugin\Producer\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;

/**
 * Class ProducerDataGrid
 *
 * @package Gekosale\Plugin\Producer\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProducerDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * {@inheritdoc}
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
            producer D
            LEFT JOIN producer_translation DT ON DT.producer_id = D.id
        ');

        $this->setGroupBy('
            D.id
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function registerEventHandlers()
    {
        $this->getXajaxManager()->registerFunctions([
            'getProducerForAjax' => [$this, 'getData'],
            'doDeleteProducer'   => [$this, 'delete']
        ]);
    }
}