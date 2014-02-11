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

class FontStyle extends TextField
{

    protected function _FormatStyles_JS ()
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

    protected function prepareAttributesJavascript ()
    {
        $attributes = Array(
            $this->formatAttributeJavascript('name', 'sName'),
            $this->formatAttributeJavascript('label', 'sLabel'),
            $this->formatAttributeJavascript('comment', 'sComment'),
            $this->formatAttributeJavascript('error', 'sError'),
            $this->formatAttributeJavascript('selector', 'sSelector'),
            $this->_FormatRules_JS(),
            $this->_FormatStyles_JS(),
            $this->formatDependencyJavascript(),
            $this->_FormatDefaults_JS()
        );
        return $attributes;
    }
}
