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

    protected function _PrepareAttributes_JS ()
    {
        $attributes = Array(
            $this->_FormatAttribute_JS('tip', 'sTip'),
            $this->_FormatAttribute_JS('direction', 'sDirection'),
            $this->_FormatAttribute_JS('short_tip', 'sShortTip'),
            $this->_FormatAttribute_JS('retractable', 'bRetractable', FE::TYPE_BOOLEAN),
            $this->_FormatAttribute_JS('default_state', 'sDefaultState'),
            $this->_FormatDependency_JS()
        );
        return $attributes;
    }

    public function Render_Static ()
    {
    }

    public function Populate ($value)
    {
    }
}
