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

use Gekosale\Core\Component;

/**
 * Class Renderer
 *
 * @package Gekosale\Core\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Renderer extends Component
{
    protected $datagrid;

    protected $html;

    public function render(DataGridInterface $datagrid)
    {
        $this->datagrid = $datagrid;
        $this->options  = $this->datagrid->getOptions();

        print_r($this->options);

        return $this->container->get('twig')->render('datagrid.twig', [
            'datagrid_options' => $this->options,
            'datagrid_html'    => $this->writeOptions(),
            'datagrid'         => $this->datagrid
        ]);

    }

    private function writeOptions()
    {
        $options['appearance'] = json_encode($this->options['appearance']);
        $options['mechanics'] = json_encode($this->options['mechanics']);
        $options['event_handlers'] = json_encode($this->options['event_handlers'],JSON_PRETTY_PRINT);


        return $options;
    }
}