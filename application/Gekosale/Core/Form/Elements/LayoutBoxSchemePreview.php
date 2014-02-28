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

namespace Gekosale\Core\Form\Elements;

/**
 * Class LayoutBoxSchemePreview
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class LayoutBoxSchemePreview extends Field implements ElementInterface
{

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        if (!isset($this->_attributes['name'])) {
            $this->_attributes['name'] = 'LayoutBoxSchemePreview_' . $this->_id;
        }
        if (isset($this->_attributes['layout_box_tpl']) && is_file($this->_attributes['layout_box_tpl'])) {
            $this->_attributes['layout_box_tpl'] = file_get_contents($this->_attributes['layout_box_tpl']);
        }
    }

    protected function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('triggers', 'asTriggers'),
            $this->formatAttributeJs('layout_box_tpl', 'sLayoutBoxTpl'),
            $this->formatAttributeJs('box_scheme', 'sBoxScheme'),
            $this->formatAttributeJs('box_name', 'sBoxName'),
            $this->formatAttributeJs('box_title', 'sBoxTitle'),
            $this->formatAttributeJs('box_content', 'sBoxContent'),
            $this->formatAttributeJs('stylesheets', 'asStylesheets'),
            $this->formatDependencyJs()
        );

        return $attributes;
    }

    public function Render_Static()
    {
    }

    public function Populate($value)
    {
    }
}
