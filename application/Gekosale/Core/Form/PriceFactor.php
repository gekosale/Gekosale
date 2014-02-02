<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @category    Gekosale 
 * @package     Gekosale\Core\Form 
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Form;

class PriceFactor
{

    protected $_type;

    protected $_value;

    const TYPE_PERCENTAGE = 'GPriceFactor.TYPE_PERCENTAGE';

    const TYPE_ADD = 'GPriceFactor.TYPE_ADD';

    const TYPE_SUBTRACT = 'GPriceFactor.TYPE_SUBTRACT';

    const TYPE_EQUALS = 'GPriceFactor.TYPE_EQUALS';

    public function __construct ($type, $value)
    {
        $this->_type = $type;
        $this->_value = $value;
    }

    public function Render ()
    {
        return "new GPriceFactor({$this->_type}, {$this->_value})";
    }
}
