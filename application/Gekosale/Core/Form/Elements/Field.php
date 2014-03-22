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

use Gekosale\Core\Form\Dependency,
    Gekosale\Core\Form\Node,
    Gekosale\Core\Form\Rules\LanguageUnique;

/**
 * Class Field
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Field extends Node
{

    protected $_value;
    protected $_globalvalue;

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        $this->_value       = '';
        $this->_globalvalue = '';
        if (isset($this->attributes['default'])) {
            $this->populate($attributes['default']);
        }
    }

    public function isValid($values = Array())
    {
        if (!isset($this->attributes['rules']) || !is_array($this->attributes['rules'])) {
            return true;
        }
        $result = true;
        foreach ($this->attributes['rules'] as $rule) {
            if (isset($this->_value) && is_array($this->_value)) {
                foreach ($this->_value as $i => $value) {
                    $skip = false;
                    if (isset($this->attributes['dependencies']) && is_array($this->attributes['dependencies'])) {
                        foreach ($this->attributes['dependencies'] as $dependency) {
                            if ((($dependency->type == Dependency::HIDE) && $dependency->evaluate($value, $i)) || (($dependency->type == Dependency::SHOW) && !$dependency->evaluate($value, $i)) || (($dependency->type == Dependency::IGNORE) && $dependency->evaluate($value, $i))) {
                                $skip = true;
                                break;
                            }
                        }
                    }
                    if (!$skip) {
                        if ($rule instanceof LanguageUnique) {
                            $rule->setLanguage($i);
                        }
                        if (($checkResult = $rule->check($value)) !== true) {
                            if (!isset($this->attributes['error']) || !is_array($this->attributes['error'])) {
                                $this->attributes['error'] = ($i > 0) ? array_fill(0, $i, '') : Array();
                            } elseif ($i > 0) {
                                $this->attributes['error'] = $this->attributes['error'] + array_fill(0, $i, '');
                            }
                            $this->attributes['error'][$i] = $checkResult;
                            $result                         = false;
                        }
                    }
                }
            } else {
                if (isset($this->attributes['dependencies']) && is_array($this->attributes['dependencies'])) {
                    foreach ($this->attributes['dependencies'] as $dependency) {
                        if ((($dependency->type == Dependency::HIDE) && $dependency->evaluate($this->_value)) || (($dependency->type == Dependency::SHOW) && !$dependency->evaluate($this->_value)) || (($dependency->type == Dependency::IGNORE) && $dependency->evaluate($this->_value))) {
                            return $result;
                        }
                    }
                }
                if (($checkResult = $rule->check($this->_value)) !== true) {
                    $this->attributes['error'] = $checkResult;
                    $result                     = false;
                }
            }
        }

        return $result;
    }

    public function populate($value)
    {
        $value        = $this->filter($value);
        $this->_value = $value;
    }

    public function getValue()
    {
        if (!isset($this->_value)) {
            return null;
        }

        return $this->_value;
    }

    protected function formatDefaultsJs()
    {
        $values = $this->getValue();
        if (empty($values)) {
            return '';
        }
        if (is_array($values)) {
            return 'asDefaults: ' . json_encode($values);
        } else {
            return 'sDefault: ' . json_encode($values);
        }
    }

    protected function formatRulesJs()
    {
        if (!isset($this->attributes['rules']) || !is_array($this->attributes['rules'])) {
            return '';
        }
        $rules = Array();
        foreach ($this->attributes['rules'] as $rule) {
            $rules[] = $rule->render();
        }

        return 'aoRules: [' . implode(', ', $rules) . ']';
    }
}
