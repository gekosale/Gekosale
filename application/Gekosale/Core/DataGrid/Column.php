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
    public function __construct (array $options)
    {
        $options = array_merge([
            'caption' => null,
            'processFunction' => null,
            'processLanguage' => false,
            'editable' => false,
            'selectable' => false,
            'filter' => NULL,
            'appearance' => [
                'width' => DataGridInterface::WIDTH_AUTO,
                'visible' => true,
                'align' => DataGridInterface::ALIGN_LEFT
            ]
        ], $options);
        
        $this->name = $options['name'];
        $this->source = $options['source'];
        $this->editable = $options['editable'];
        $this->selectable = $options['editable'];
        $this->filter = $options['filter'];
        $this->caption = $options['caption'];
        $this->appearance = $options['appearance'];
        $this->processFunction = $options['processFunction'];
        $this->processLanguage = (bool) $options['processLanguage'];
        
        if ($this->processFunction != NULL && ! $this->processFunction instanceof Closure) {
            throw new \InvalidArgumentException('DataGrid process function should be Closure');
        }
    }

    public function render ()
    {
        $options = json_encode([
            'id' => $this->name,
            'caption' => $this->caption,
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