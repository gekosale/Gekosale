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
 * Class TextField
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class TextField extends Field implements ElementInterface
{
    const SIZE_SHORT  = 'short';
    const SIZE_MEDIUM = 'medium';
    const SIZE_LONG   = 'long';

    public function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatAttributeJs('comment', 'sComment'),
            $this->formatAttributeJs('suffix', 'sSuffix'),
            $this->formatAttributeJs('prefix', 'sPrefix'),
            $this->formatAttributeJs('selector', 'sSelector'),
            $this->formatAttributeJs('wrap', 'sWrapClass'),
            $this->formatAttributeJs('class', 'sClass'),
            $this->formatAttributeJs('css_attribute', 'sCssAttribute'),
            $this->formatAttributeJs('max_length', 'iMaxLength'),
            $this->formatAttributeJs('error', 'sError'),
            $this->formatRepeatableJs(),
            $this->formatRulesJs(),
            $this->formatDependencyJs(),
            $this->formatDefaultsJs()
        );

        return $attributes;
    }
}
