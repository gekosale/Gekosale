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
     * Column name
     *
     * @var string
     */
    protected $name;

    /**
     * Column source
     *
     * @var string
     */
    protected $source;

    /**
     * Function to post-process column value after datagrid initialization
     *
     * @var null
     */
    protected $processFunction = null;

    /**
     * True if column source is translateable false otherwise
     *
     * @var bool
     */
    protected $processLanguage = false;

    /**
     * @param $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function render()
    {
        $options = json_encode([
            'id'         => $this->name,
            'caption'    => $this->caption,
            'appearance' => $this->appearance
        ]);

        $script = "
            var column_{$this->name} = new GF_Datagrid_Column({
               {$options}
            });
        ";

        return $script;
    }
} 