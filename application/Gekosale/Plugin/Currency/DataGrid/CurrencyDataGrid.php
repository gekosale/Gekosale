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
namespace Gekosale\Plugin\Currency\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;

/**
 * Class CurrencyDataGrid
 *
 * @package Gekosale\Plugin\Currency\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CurrencyDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setTableData([
            'id'     => [
                'source' => 'C.id'
            ],
            'name'   => [
                'source' => 'C.name'
            ],
            'symbol' => [
                'source' => 'C.symbol'
            ]
        ]);

        $this->setFrom('
            currency C
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
            'getCurrencyForAjax' => [$this, 'getData'],
            'doDeleteCurrency'   => [$this, 'delete']
        ]);
    }
}