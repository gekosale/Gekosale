<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Plugin
 * @subpackage  Gekosale\Plugin\CurrencyDataGrid
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Plugin\Currency\DataGrid;

use Gekosale\Core\DataGrid;
use Gekosale\Core\DataGrid\DataGridInterface;
use Gekosale\Core\DataGrid\Column;
use Gekosale\Core\DataGrid\Filter\Between;
use Foo;

class CurrencyDataGrid extends DataGrid implements DataGridInterface
{

    public function init ()
    {
        $foo = 1;

        $this->setName('currency');
        
        $this->addColumn(new Column([
            'name' => 'id',
            'source' => 'V.id',
            'editable' => true,
            'caption' => $this->trans('TXT_ID'),
            'filter' => DataGridInterface::FILTER_BETWEEN,
            'appearance' => [
                'width' => 150,
                'visible' => false,
                'align' => DataGridInterface::ALIGN_LEFT
            ]
        ]));
        
        $this->addColumn(new Column([
            'name' => 'id2',
            'source' => 'V.id2'
        ]));
        
        return $this;
    }
}