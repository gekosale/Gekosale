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

use Closure;

/**
 * Class Column
 *
 * @package Gekosale\Core\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Column
{

    /**
     * Column options
     *
     * @var array
     */
    protected $options;

    /**
     * @param $options
     */
    public function __construct($id, array $options = [])
    {

        $this->options = array_merge([
            'id'         => $id,
            'caption'    => '',
            'sorting'    => [
                'default_order' => DataGridInterface::SORT_DIR_DESC
            ],
            'editable'   => false,
            'selectable' => false,
            'appearance' => [
                'width'   => DataGridInterface::WIDTH_AUTO,
                'align'   => DataGridInterface::ALIGN_LEFT,
                'visible' => true
            ],
            'filter'     => [
                'type' => DataGridInterface::FILTER_NONE
            ]
        ], $options);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
} 