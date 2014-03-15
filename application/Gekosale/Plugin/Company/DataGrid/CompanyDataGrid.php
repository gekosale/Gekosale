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
namespace Gekosale\Plugin\Company\DataGrid;

use Gekosale\Core\DataGrid;
use Gekosale\Core\DataGrid\DataGridInterface;

/**
 * Class CompanyDataGrid
 *
 * @package Gekosale\Plugin\Company\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CompanyDataGrid extends DataGrid implements DataGridInterface
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->query = $this->getDb()
            ->table('company')
            ->groupBy('company.id');
    }

    /**
     * {@inheritdoc}
     */
    public function registerEventHandlers()
    {
        $this->getXajaxManager()->registerFunctions([
            'getCompanyForAjax' => [$this, 'getData'],
            'doDeleteCompany'   => [$this, 'delete']
        ]);
    }
}