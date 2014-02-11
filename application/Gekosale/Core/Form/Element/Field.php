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

use Gekosale\Core\Form\Node;

class Field extends Node
{

    protected $_value;

    protected $_globalvalue;

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        $this->_value = '';
        $this->_globalvalue = '';
        if (isset($this->_attributes['default'])) {
            $this->Populate($attributes['default']);
        }
    }

    public function validate ($values = Array())
    {
        if (! isset($this->_attributes['rules']) || ! is_array($this->_attributes['rules'])) {
            return true;
        }
        $result = true;
        foreach ($this->_attributes['rules'] as $rule) {
            if (isset($this->_value) && is_array($this->_value)) {
                foreach ($this->_value as $i => $value) {
                    $skip = false;
                    if (isset($this->_attributes['dependencies']) && is_array($this->_attributes['dependencies'])) {
                        foreach ($this->_attributes['dependencies'] as $dependency) {
                            if ((($dependency->type == \FormEngine\Dependency::HIDE) && $dependency->Evaluate($value, $i)) || (($dependency->type == \FormEngine\Dependency::SHOW) && ! $dependency->Evaluate($value, $i)) || (($dependency->type == \FormEngine\Dependency::IGNORE) && $dependency->Evaluate($value, $i))) {
                                $skip = true;
                                break;
                            }
                        }
                    }
                    if (! $skip) {
                        if ($rule instanceof FE_RuleLanguageUnique) {
                            $rule->setLanguage($i);
                        }
                        if (($checkResult = $rule->Check($value)) !== true) {
                            if (! isset($this->_attributes['error']) || ! is_array($this->_attributes['error'])) {
                                $this->_attributes['error'] = ($i > 0) ? array_fill(0, $i, '') : Array();
                            }
                            elseif ($i > 0) {
                                $this->_attributes['error'] = $this->_attributes['error'] + array_fill(0, $i, '');
                            }
                            $this->_attributes['error'][$i] = $checkResult;
                            $result = false;
                        }
                    }
                }
            }
            else {
                if (isset($this->_attributes['dependencies']) && is_array($this->_attributes['dependencies'])) {
                    foreach ($this->_attributes['dependencies'] as $dependency) {
                        if ((($dependency->type == \FormEngine\Dependency::HIDE) && $dependency->Evaluate($this->_value)) || (($dependency->type == \FormEngine\Dependency::SHOW) && ! $dependency->Evaluate($this->_value)) || (($dependency->type == \FormEngine\Dependency::IGNORE) && $dependency->Evaluate($this->_value))) {
                            return $result;
                        }
                    }
                }
                if (($checkResult = $rule->Check($this->_value)) !== true) {
                    $this->_attributes['error'] = $checkResult;
                    $result = false;
                }
            }
        }
        return $result;
    }

    public function Populate ($value)
    {
        $value = $this->filter($value);
        $this->_value = $value;
    }

    public function PopulateGlobal ($globalvalue)
    {
        $globalvalue = $this->filter($globalvalue);
        $this->_globalvalue = $globalvalue;
    }

    public function getValue ()
    {
        if (! isset($this->_value)) {
            return null;
        }
        return $this->_value;
    }

    protected function _FormatDefaults_JS ()
    {
        $values = $this->getValue();
        if (empty($values)) {
            return '';
        }
        if (is_array($values)) {
            return 'asDefaults: ' . json_encode($values);
        }
        else {
            return 'sDefault: ' . json_encode($values);
        }
    }

    protected function _FormatRules_JS ()
    {
        if (! isset($this->_attributes['rules']) || ! is_array($this->_attributes['rules'])) {
            return '';
        }
        $rules = Array();
        foreach ($this->_attributes['rules'] as $rule) {
            $rules[] = $rule->render();
        }
        return 'aoRules: [' . implode(', ', $rules) . ']';
    }
}
