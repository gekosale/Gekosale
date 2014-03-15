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
        $this->registerEventHandlers();

        $this->addColumn('id', [
            'source' => 'currency.id'
        ]);

        $this->addColumn('name', [
            'source' => 'currency.name'
        ]);

        $this->addColumn('symbol', [
            'source' => 'currency.symbol'
        ]);

        $this->query = $this->getDb()
            ->table('currency')
            ->groupBy('currency.id');
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