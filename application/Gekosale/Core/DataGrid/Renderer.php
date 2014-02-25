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
namespace Gekosale\Core\DataGrid;

use Gekosale\Core\DataGrid\DataGridInterface;

/**
 * Class Renderer
 *
 * @package Gekosale\Core\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Renderer
{

    protected $datagrid;

    protected $container;

    public function __construct (DataGridInterface $datagrid)
    {
        $this->datagrid = $datagrid;
        $this->template = $this->datagrid->getContainer()->get('twig');
    }

    public function toHtml ()
    {
        return $this->template->render('datagrid.twig', [
            'datagrid' => $this->datagrid
        ]);
    }
} 