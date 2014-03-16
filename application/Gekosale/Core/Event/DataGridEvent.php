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
namespace Gekosale\Core\Event;

use Gekosale\Core\DataGrid;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class DataGridEvent
 *
 * @package Gekosale\Core\Event
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class DataGridEvent extends Event
{

    protected $datagrid;

    /**
     * Constructor
     *
     * @param DataGrid $datagrid
     */
    public function __construct(DataGrid $datagrid)
    {
        $this->datagrid = $datagrid;
    }

    /**
     * Returns DataGrid
     *
     * @return mixed
     */
    public function getDataGrid()
    {
        return $this->datagrid;
    }
}