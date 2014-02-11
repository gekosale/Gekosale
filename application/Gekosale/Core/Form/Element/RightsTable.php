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

class RightsTable extends Field
{

    public function Populate ($value)
    {
        if (is_array($this->_value)) {
            foreach ($this->_value as $c => &$cV) {
                foreach ($cV as $a => &$aV) {
                    if (isset($value[$c][$a]) && $value[$c][$a]) {
                        $aV = 1;
                    }
                    else {
                        $aV = 0;
                    }
                }
            }
        }
        else {
            $this->_value = Array();
        }
        if (is_array($value)) {
            foreach ($value as $c => $cV2) {
                if (! isset($this->_value[$c])) {
                    $this->_value[$c] = $cV2;
                }
            }
        }
    }

    protected function prepareAttributesJavascript ()
    {
        $attributes = Array(
            $this->formatAttributeJavascript('name', 'sName'),
            $this->formatAttributeJavascript('label', 'sLabel'),
            $this->formatAttributeJavascript('comment', 'sComment'),
            $this->formatAttributeJavascript('error', 'sError'),
            $this->formatAttributeJavascript('controllers', 'asControllers', FE::TYPE_OBJECT),
            $this->formatAttributeJavascript('actions', 'asActions', FE::TYPE_OBJECT),
            $this->_FormatRules_JS(),
            $this->formatDependencyJavascript(),
            $this->_FormatDefaults_JS()
        );
        return $attributes;
    }

    protected function _FormatDefaults_JS ()
    {
        $values = $this->GetValue();
        if (empty($values)) {
            return '';
        }
        return 'aabDefaults: ' . json_encode($values);
    }
}
