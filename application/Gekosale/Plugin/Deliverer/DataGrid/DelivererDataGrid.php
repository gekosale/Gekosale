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
     * {@inheritdoc}
     */
    public function init()
    {
        $this->registerEventHandlers();

        $this->addColumn('id', [
            'source' => 'deliverer.id'
        ]);

        $this->addColumn('name', [
            'source' => 'deliverer_translation.name'
        ]);

        $this->query = $this->getDb()
            ->table('deliverer')
            ->join('deliverer_translation', 'deliverer_translation.deliverer_id', '=', 'deliverer.id')
            ->groupBy('deliverer.id');
    }

    /**
     * {@inheritdoc}
     */
    public function registerEventHandlers()
    {
        $this->getXajaxManager()->registerFunctions([
            'getDelivererForAjax' => [$this, 'getData'],
            'doDeleteDeliverer'   => [$this, 'delete']
        ]);
    }
}