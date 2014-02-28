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
 * Class FontStyle
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class FontStyle extends TextField implements ElementInterface
{

    public function formatStylesJs()
    {

        $options[] = "{sValue: 'Arial,Arial,Helvetica,sans-serif', sLabel: 'Arial'}";
        $options[] = "{sValue: 'Arial Black,Arial Black,Gadget,sans-serif', sLabel: 'Arial Black'}";
        $options[] = "{sValue: 'Comic Sans MS,Comic Sans MS,cursive', sLabel: 'Comic Sans MS'}";
        $options[] = "{sValue: 'Courier New,Courier New,Courier,monospace', sLabel: 'Courier New'}";
        $options[] = "{sValue: 'Georgia,Georgia,serif', sLabel: 'Georgia'}";
        $options[] = "{sValue: 'Impact,Charcoal,sans-serif', sLabel: 'Impact'}";
        $options[] = "{sValue: 'Lucida Console,Monaco,monospace', sLabel: 'Lucida Console'}";
        $options[] = "{sValue: 'Lucida Sans Unicode,Lucida Grande,sans-serif', sLabel: 'Lucida Sans'}";
        $options[] = "{sValue: 'Palatino Linotype,Book Antiqua,Palatino,serif', sLabel: 'Palatino Linotype'}";
        $options[] = "{sValue: 'Tahoma,Geneva,sans-serif', sLabel: 'Tahoma'}";
        $options[] = "{sValue: 'Times New Roman,Times,serif', sLabel: 'Times New Roman'}";
        $options[] = "{sValue: 'Trebuchet MS,Helvetica,sans-serif', sLabel: 'Trebuchet'}";
        $options[] = "{sValue: 'Verdana,Geneva,sans-serif', sLabel: 'Verdana'}";

        return 'aoTypes: [' . implode(', ', $options) . ']';
    }

    public function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatAttributeJs('comment', 'sComment'),
            $this->formatAttributeJs('error', 'sError'),
            $this->formatAttributeJs('selector', 'sSelector'),
            $this->formatRulesJs(),
            $this->formatStylesJs(),
            $this->formatDependencyJs(),
            $this->formatDefaultsJs()
        );

        return $attributes;
    }

}
