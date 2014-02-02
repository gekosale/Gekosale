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

use Gekosale\Core\Form\Node;

class StaticText extends Node
{

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        $this->_attributes['name'] = '';
    }

    protected function _PrepareAttributes_JS ()
    {
        $attributes = Array(
            $this->_FormatAttribute_JS('text', 'sText'),
            $this->_FormatAttribute_JS('class', 'sClass'),
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
