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
        $this->registerEventHandlers();

        $this->addColumn('id', [
            'source' => 'producer.id'
        ]);

        $this->addColumn('name', [
            'source' => 'producer_translation.name'
        ]);

        $this->query = $this->getDb()
            ->table('producer')
            ->join('producer_translation', 'producer_translation.producer_id', '=', 'producer.id')
            ->groupBy('producer.id');
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