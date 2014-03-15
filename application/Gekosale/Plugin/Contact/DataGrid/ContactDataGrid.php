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
        $this->registerEventHandlers();

        $this->addColumn('id', [
            'source' => 'contact.id'
        ]);

        $this->addColumn('name', [
            'source' => 'contact_translation.name'
        ]);

        $this->query = $this->getDb()
            ->table('contact')
            ->join('contact_translation', 'contact_translation.contact_id', '=', 'contact.id')
            ->groupBy('contact.id');
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