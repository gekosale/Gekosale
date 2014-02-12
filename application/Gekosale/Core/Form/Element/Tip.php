<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @category    Gekosale 
 * @package     Gekosale\Core\Form
 * @subpackage  Gekosale\Core\Form\Element
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Form\Element;

class Tip extends \FormEngine\Node
{

    const UP = 'up';

    const DOWN = 'down';

    const EXPANDED = 'expanded';

    const RETRACTED = 'retracted';

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        $this->_attributes['name'] = '';
        if (isset($this->_attributes['short_tip']) && strlen($this->_attributes['short_tip'])) {
            $this->_attributes['retractable'] = true;
        }
    }

    protected function prepareAttributesJavascript ()
    {
        $attributes = Array(
            $this->formatAttributeJavascript('tip', 'sTip'),
            $this->formatAttributeJavascript('direction', 'sDirection'),
            $this->formatAttributeJavascript('short_tip', 'sShortTip'),
            $this->formatAttributeJavascript('retractable', 'bRetractable', FE::TYPE_BOOLEAN),
            $this->formatAttributeJavascript('default_state', 'sDefaultState'),
            $this->formatDependencyJavascript()
        );
        return $attributes;
    }

    public function renderStatic ()
    {
    }

    public function populate ($value)
    {
    }
}
