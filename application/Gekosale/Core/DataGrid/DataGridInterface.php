<?php

namespace Gekosale\Core\DataGrid;

/**
 * Interface DataGridInterface
 *
 * @package Gekosale\Core\DataGrid
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
interface DataGridInterface
{

    const SORT_DIR_ASC = 1;

    const SORT_DIR_DESC = 2;

    const ALIGN_LEFT = 1;

    const ALIGN_CENTER = 2;

    const ALIGN_RIGHT = 3;

    const FILTER_NONE = 0;

    const FILTER_INPUT = 1;

    const FILTER_BETWEEN = 2;

    const FILTER_SELECT = 3;

    const FILTER_AUTOSUGGEST = 4;

    const WIDTH_AUTO = 0;

    /**
     * DataGrid initialization
     */
    public function init ();

    public function getData ($request, $processFunction);
}