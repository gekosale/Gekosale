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
 * Class Submit
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Submit extends Node implements ElementInterface
{

    public function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('class', 'sClass'),
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatAttributeJs('icon', 'sIcon'),
            $this->formatDependencyJs()
        );

        return $attributes;
    }

    public function getValue()
    {
        return '';
    }

    public function Populate($value)
    {
    }

}
