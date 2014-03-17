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
use Gekosale\Core\DataGrid;

/**
 * Class Renderer
 *
 * @package Gekosale\Core\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Renderer extends Component
{

    public function render(DataGrid $dataGrid)
    {
        print_r($dataGrid->getOptions());
        print_r($dataGrid->getColumns());
        die();
    }
} 