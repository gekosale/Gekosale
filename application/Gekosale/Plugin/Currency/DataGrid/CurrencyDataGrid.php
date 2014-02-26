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
    public function init()
    {
        $this->getXajax()->registerFunction([
            'getCurrencyForAjax',
            $this,
            'getData'
        ]);

        $this->getXajax()->registerFunction([
            'doDeleteCurrency',
            $this,
            'doDeleteCurrency'
        ]);

        $this->setTableData([
            'id'     => Array(
                'source' => 'C.id'
            ),
            'name'   => Array(
                'source' => 'C.name'
            ),
            'symbol' => Array(
                'source' => 'C.symbol'
            )
        ]);

        $this->setFrom('
            currency C
        ');

        $this->setGroupBy('
            C.id
        ');
    }
}