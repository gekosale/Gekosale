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

class Select extends OptionedField
{

    protected function prepareAttributesJavascript ()
    {
        $attributes = Array(
            $this->formatAttributeJavascript('name', 'sName'),
            $this->formatAttributeJavascript('label', 'sLabel'),
            $this->formatAttributeJavascript('comment', 'sComment'),
            $this->formatAttributeJavascript('suffix', 'sSuffix'),
            $this->formatAttributeJavascript('prefix', 'sPrefix'),
            $this->formatAttributeJavascript('error', 'sError'),
            $this->formatAttributeJavascript('selector', 'sSelector'),
            $this->formatAttributeJavascript('css_attribute', 'sCssAttribute'),
            $this->formatAttributeJavascript('addable', 'bAddable'),
            $this->formatAttributeJavascript('onAdd', 'fOnAdd', \FormEngine\FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('add_item_prompt', 'sAddItemPrompt'),
            $this->formatRepeatableJavascript(),
            $this->_FormatRules_JS(),
            $this->formatDependencyJavascript(),
            $this->_FormatOptions_JS(),
            $this->_FormatDefaults_JS()
        );
        
        return $attributes;
    }
}
