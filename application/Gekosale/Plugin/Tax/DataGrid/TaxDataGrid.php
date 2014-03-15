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
namespace Gekosale\Plugin\Tax\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;

/**
 * Class TaxDataGrid
 *
 * @package Gekosale\Plugin\Tax\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class TaxDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->registerEventHandlers();

        $this->addColumn('id', [
            'source' => 'tax.id'
        ]);

        $this->addColumn('name', [
            'source' => 'tax_translation.name'
        ]);

        $this->query = $this->getDb()
            ->table('tax')
            ->join('tax_translation', 'tax_translation.tax_id', '=', 'tax.id')
            ->groupBy('tax.id');
    }

    /**
     * {@inheritdoc}
     */
    public function registerEventHandlers()
    {
        $this->getXajaxManager()->registerFunctions([
            'getTaxForAjax' => [$this, 'getData'],
            'doDeleteTax'   => [$this, 'delete']
        ]);
    }
}