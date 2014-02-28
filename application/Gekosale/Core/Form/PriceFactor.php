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

namespace Gekosale\Core\Form;

/**
 * Class PriceFactor
 *
 * @package Gekosale\Core\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class PriceFactor
{

    protected $_type;

    protected $_value;

    const TYPE_PERCENTAGE = 'GPriceFactor.TYPE_PERCENTAGE';
    const TYPE_ADD        = 'GPriceFactor.TYPE_ADD';
    const TYPE_SUBTRACT   = 'GPriceFactor.TYPE_SUBTRACT';
    const TYPE_EQUALS     = 'GPriceFactor.TYPE_EQUALS';

    public function __construct($type, $value)
    {
        $this->_type  = $type;
        $this->_value = $value;
    }

    public function Render()
    {
        return "new GPriceFactor({$this->_type}, {$this->_value})";
    }
}
