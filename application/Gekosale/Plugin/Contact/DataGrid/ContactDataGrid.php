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
namespace Gekosale\Plugin\Contact\DataGrid;

use Gekosale\Core\DataGrid,
    Gekosale\Core\DataGrid\DataGridInterface;

/**
 * Class ContactDataGrid
 *
 * @package Gekosale\Plugin\Contact\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ContactDataGrid extends DataGrid implements DataGridInterface
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
                'source' => 'CT.name'
            ],
        ]);

        $this->setFrom('
            contact C
            LEFT JOIN contact_translation CT ON CT.contact_id = C.id
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
            'getContactForAjax' => [$this, 'getData'],
            'doDeleteContact'   => [$this, 'delete']
        ]);
    }
}