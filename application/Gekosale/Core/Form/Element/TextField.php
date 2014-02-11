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

class TextField extends Field
{

    const SIZE_SHORT = 'short';

    const SIZE_MEDIUM = 'medium';

    const SIZE_LONG = 'long';

    protected function prepareAttributesJavascript ()
    {
        $attributes = Array(
            $this->formatAttributeJavascript('name', 'sName'),
            $this->formatAttributeJavascript('label', 'sLabel'),
            $this->formatAttributeJavascript('comment', 'sComment'),
            $this->formatAttributeJavascript('suffix', 'sSuffix'),
            $this->formatAttributeJavascript('prefix', 'sPrefix'),
            $this->formatAttributeJavascript('selector', 'sSelector'),
            $this->formatAttributeJavascript('wrap', 'sWrapClass'),
            $this->formatAttributeJavascript('class', 'sClass'),
            $this->formatAttributeJavascript('css_attribute', 'sCssAttribute'),
            $this->formatAttributeJavascript('max_length', 'iMaxLength'),
            $this->formatAttributeJavascript('error', 'sError'),
            $this->formatRepeatableJavascript(),
            $this->_FormatRules_JS(),
            $this->formatDependencyJavascript(),
            $this->_FormatDefaults_JS()
        );
        
        return $attributes;
    }
}
