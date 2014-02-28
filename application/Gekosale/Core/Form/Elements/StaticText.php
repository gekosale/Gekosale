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

use Gekosale\Core\Form\Node;

/**
 * Class StaticText
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class StaticText extends Node implements ElementInterface
{

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        $this->_attributes['name'] = '';
    }

    public function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('text', 'sText'),
            $this->formatAttributeJs('class', 'sClass'),
            $this->formatDependencyJs()
        );

        return $attributes;
    }

    public function renderStatic()
    {
    }

    public function populate($value)
    {
    }

}
