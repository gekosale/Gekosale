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
namespace Gekosale\Core\DataGrid\Filter;

/**
 * Class Between
 *
 * @package Gekosale\Core\DataGrid\Filter
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Between implements DataGridFilterInterface
{

    public function render ()
    {
        return 'type: GF_Datagrid.FILTER_BETWEEN';
    }
} 