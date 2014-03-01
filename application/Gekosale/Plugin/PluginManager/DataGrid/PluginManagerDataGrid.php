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
namespace Gekosale\Plugin\PluginManager\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;

/**
 * Class PluginManagerDataGrid
 *
 * @package Gekosale\Plugin\PluginManager\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class PluginManagerDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setTableData([
            'id'   => [
                'source' => 'C.id'
            ],
            'name' => [
                'source' => 'C.name'
            ],
        ]);

        $this->setFrom('
            plugin_manager C
        ');

        $this->setGroupBy('
            C.id
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function registerEventHandlers()
    {
        $this->getXajaxManager()->registerFunctions([
            'getPluginManagerForAjax' => [$this, 'getData'],
            'doDeletePluginManager'   => [$this, 'delete']
        ]);
    }
}