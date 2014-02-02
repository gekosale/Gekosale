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

class LayoutBoxSchemePreview extends Field
{

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        if (! isset($this->_attributes['name'])) {
            $this->_attributes['name'] = 'LayoutBoxSchemePreview_' . $this->_id;
        }
        if (isset($this->_attributes['layout_box_tpl']) && is_file($this->_attributes['layout_box_tpl'])) {
            $this->_attributes['layout_box_tpl'] = file_get_contents($this->_attributes['layout_box_tpl']);
        }
    }

    protected function _PrepareAttributes_JS ()
    {
        $attributes = Array(
            $this->_FormatAttribute_JS('name', 'sName'),
            $this->_FormatAttribute_JS('triggers', 'asTriggers'),
            $this->_FormatAttribute_JS('layout_box_tpl', 'sLayoutBoxTpl'),
            $this->_FormatAttribute_JS('box_scheme', 'sBoxScheme'),
            $this->_FormatAttribute_JS('box_name', 'sBoxName'),
            $this->_FormatAttribute_JS('box_title', 'sBoxTitle'),
            $this->_FormatAttribute_JS('box_content', 'sBoxContent'),
            $this->_FormatAttribute_JS('stylesheets', 'asStylesheets'),
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
