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

namespace Gekosale\Core\Form;

/**
 * Class Node
 *
 * @package Gekosale\Core\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
abstract class Node
{

    public $form;

    public $parent;

    protected $_id;

    protected $_attributes;

    protected $_renderMode;

    protected $_tabs;

    protected $_jsNodeName;

    protected $_xajaxMethods;

    protected static $_nextId = 0;

    public function __construct($attributes)
    {
        $this->_id           = self::$_nextId++;
        $this->_attributes   = $attributes;
        $this->_renderMode   = 'Static';
        $this->_tabs         = '';
        $class               = explode('\\', get_class($this));
        $this->_jsNodeName   = 'GForm' . end($class);
        $this->form          = null;
        $this->parent        = null;
        $this->_xajaxMethods = Array();
    }

    public function Render($mode = 'JS', $tabs = '')
    {
        $this->_tabs       = $tabs;
        $this->_renderMode = $mode;
        $renderFunction    = 'Render_' . $mode;
        $lines             = explode("\n", $this->$renderFunction());
        foreach ($lines as &$line) {
            $line = $this->_tabs . $line;
        }

        return implode("\n", $lines);
    }

    public function AddRule($rule)
    {
        if (!isset($this->_attributes['rules']) or !is_array($this->_attributes['rules'])) {
            $this->_attributes['rules'] = Array();
        }
        $this->_attributes['rules'][] = $rule;
    }

    public function ClearRules()
    {
        $this->_attributes['rules'] = Array();
    }

    public function AddFilter($filter)
    {
        if (!isset($this->_attributes['filters']) or !is_array($this->_attributes['filters'])) {
            $this->_attributes['filters'] = Array();
        }
        $this->_attributes['filters'][] = $filter;
    }

    public function SetFilter($filter)
    {
        if (!isset($this->_attributes['filters']) or !is_array($this->_attributes['filters'])) {
            $this->_attributes['filters'] = Array();
        }
        $this->_attributes['filters'][] = $filter;
    }

    public function ClearFilters()
    {
        $this->_attributes['filters'] = Array();
    }

    public function AddDependency($dependency)
    {
        if (!isset($this->_attributes['dependencies']) or !is_array($this->_attributes['dependencies'])) {
            $this->_attributes['dependencies'] = Array();
        }
        $this->_attributes['dependencies'][] = $dependency;
    }

    protected function _Filter($values)
    {
        if (!isset($this->_attributes['filters']) or !is_array($this->_attributes['filters'])) {
            return $values;
        }
        if (is_array($values)) {
            foreach ($values as &$value) {
                foreach ($this->_attributes['filters'] as $filter) {
                    $value = $filter->Filter($value);
                }
            }
        } else {
            foreach ($this->_attributes['filters'] as $filter) {
                $values = $filter->Filter($values);
            }
        }

        return $values;
    }

    public function GetName()
    {
        return $this->_attributes['name'];
    }

    protected function _HarvestValues($node, $levels)
    {
        $value = $node->GetValue();
        foreach ($levels as $level) {
            if (isset($value[$level])) {
                $value = $value[$level];
            } else {
                return '';
            }
        }

        return $value;
    }

    protected function _HarvestErrors($node, $levels)
    {
        if (!isset($node->_attributes['error'])) {
            return '';
        }
        $value = $node->_attributes['error'];
        foreach ($levels as $level) {
            if (isset($value[$level])) {
                $value = $value[$level];
            } else {
                return '';
            }
        }

        return $value;
    }

    protected function _Harvest($action, $levelsCount = 0, $levels = Array())
    {
        if (isset($this->_children)) {
            $array = Array();
            foreach ($this->_children as $child) {
                $name = $child->GetName();
                if (empty($name)) {
                    continue;
                }
                if (get_class($this) == 'FormEngine\Elements\FieldsetRepeatable') {
                    $repetitions = $child->_HarvestRepetitions($levelsCount);
                    foreach ($repetitions as $repetition) {
                        $levelsCopy                = $levels + Array(
                                $repetition
                            );
                        $array[$repetition][$name] = $child->_Harvest($action, $levelsCount + 1, $levelsCopy);
                    }
                } else {
                    $array[$name] = $child->_Harvest($action, $levelsCount, $levels);
                }
            }

            return $array;
        } else {
            if (is_array($action)) {
                return call_user_func($action, $this, $levels);
            }

            return $action($this, $levels);
        }
    }

    protected function _HarvestRepetitions($level = 0)
    {
        if (isset($this->_children)) {
            $array = Array();
            foreach ($this->_children as $child) {
                array_push($array, $child->_HarvestRepetitions($level));
            }

            return array_unique($array);
        } else {
            $value       = $this->GetValue();
            $repetitions = $this->_ExtractRepetitions($value, $level);

            return array_unique($repetitions);
        }
    }

    protected function _ExtractRepetitions($array, $targetLevel, $level = 0)
    {
        if ($targetLevel >= $level) {
            if (is_array($array)) {
                return array_keys($array);
            }

            return Array();
        }
        $repetitions = Array();
        foreach ($array as $key => $value) {
            array_push($repetitions, $this->_ExtractRepetitions($value, $targetLevel, $level + 1));
        }

        return $repetitions;
    }

    protected function _FormatAttributes_JS($attributes)
    {
        $attributes       = array_merge($attributes, $this->_PrepareAutoAttributes_JS());
        $attributesString = "\n";
        foreach ($attributes as $attribute) {
            if (!empty($attribute)) {
                $attributesString .= $this->_tabs . $attribute . ",\n";
            }
        }

        return substr($attributesString, 0, -2) . "\n";
    }

    protected function formatAttributeJs($attributeName, $name = null, $type = FE::TYPE_STRING)
    {
        if ($name == null) {
            if (!isset($this->_attributes[$attributeName])) {
                if ($type == FE::TYPE_FUNCTION) {
                    return 'null';
                } elseif ($type == FE::TYPE_NUMBER) {
                    return '0';
                } elseif ($type == FE::TYPE_ARRAY) {
                    return '[]';
                } elseif ($type == FE::TYPE_OBJECT) {
                    return '{}';
                } elseif ($type == FE::TYPE_BOOLEAN) {
                    return 'false';
                }

                return '\'\'';
            }
            if ($type == FE::TYPE_FUNCTION) {
                return $this->_attributes[$attributeName];
            } elseif ($type == FE::TYPE_NUMBER) {
                return $this->_attributes[$attributeName];
            } elseif ($type == FE::TYPE_ARRAY) {
                return json_encode($this->_attributes[$attributeName]);
            } elseif ($type == FE::TYPE_OBJECT) {
                return json_encode($this->_attributes[$attributeName]);
            } elseif ($type == FE::TYPE_BOOLEAN) {
                return $this->_attributes[$attributeName] ? 'true' : 'false';
            }

            return str_replace(Array(
                "\r\n",
                "\n"
            ), '\n', '\'' . addslashes($this->_attributes[$attributeName]) . '\'');
        }
        if (!isset($this->_attributes[$attributeName])) {
            return '';
        }
        $value = $this->_attributes[$attributeName];
        if ($type == FE::TYPE_ARRAY) {
            return $name . ': ' . json_encode($value);
        } elseif ($type == FE::TYPE_OBJECT) {
            return $name . ': ' . json_encode($value);
        } elseif (is_array($value)) {
            foreach ($value as &$valuePart) {
                if ($type == FE::TYPE_FUNCTION) {
                    $valuePart = '' . ($valuePart) . '';
                } elseif ($type == FE::TYPE_NUMBER) {
                    $valuePart = '' . ($valuePart) . '';
                } else {
                    $valuePart = '\'' . addslashes($valuePart) . '\'';
                }
            }

            return str_replace("\n", '\n', $name . ': [' . implode(', ', $value) . ']');
        } else {
            if ($type == FE::TYPE_FUNCTION) {
                return $name . ': ' . ($value) . '';
            } elseif ($type == FE::TYPE_NUMBER) {
                return $name . ': ' . ($value) . '';
            } elseif ($type == FE::TYPE_BOOLEAN) {
                return $name . ': ' . ($value ? 'true' : 'false') . '';
            } else {
                return str_replace(Array(
                    "\r\n",
                    "\n"
                ), '\n', $name . ': \'' . addslashes($value) . '\'');
            }
        }
    }

    protected function formatRepeatableJs()
    {
        if ((isset($this->_attributes['repeat_min']) && ($this->_attributes['repeat_min'] != 1)) || (isset($this->_attributes['repeat_max']) && ($this->_attributes['repeat_max'] != 1))) {
            $min
                = (isset($this->_attributes['repeat_min']) && is_numeric($this->_attributes['repeat_min'])) ? $this->_attributes['repeat_min'] : 1;
            $max
                = (isset($this->_attributes['repeat_max']) && is_numeric($this->_attributes['repeat_max'])) ? $this->_attributes['repeat_max'] : 1;
            if (isset($this->_attributes['repeat_max']) && ($this->_attributes['repeat_max'] == FE::INFINITE)) {
                $max = 'GForm.INFINITE';
            }

            return "oRepeat: {iMin: {$min}, iMax: {$max}}";
        }

        return '';
    }

    protected function formatDependencyJs()
    {
        $dependencies = Array();
        if (isset($this->_attributes['dependencies']) && is_array($this->_attributes['dependencies'])) {
            foreach ($this->_attributes['dependencies'] as $dependency) {
                $dependencies[] = $dependency->Render_JS();
            }
        }
        if (count($dependencies)) {
            return 'agDependencies: [' . implode(', ', $dependencies) . ']';
        }

        return '';
    }

    protected function formatFactorJs($factor, $name)
    {
        return "{$name}: {$this->_attributes[$factor]->Render()}";
    }

    public function Render_JS()
    {
        $render = "
			{fType: {$this->_jsNodeName},{$this->_FormatAttributes_JS($this->prepareAttributesJs())}}
		";

        return $render;
    }

    protected function prepareAttributesJs()
    {
        return Array();
    }

    public function Render_Static()
    {
    }

    public function Validate($values = Array())
    {
        return true;
    }

    protected function _IsIterated($array)
    {
        if (is_numeric(key($array)) or substr(key($array), 0, 4) == 'new-') {
            return true;
        }

        return false;
    }

    public function Populate($value)
    {
    }

    protected function _PrepareAutoAttributes_JS()
    {
        $attributes = Array();
        $attributes = array_merge($attributes, $this->_xajaxMethods);

        return $attributes;
    }

    protected function _RegisterXajaxMethod($name, $callback)
    {
        $jsName                   = $name . '_' . $this->_id;
        $this->_attributes[$name] = 'xajax_' . $jsName;
        App::getRegistry()->xajaxInterface->registerFunction(array(
            $jsName,
            $callback[0],
            $callback[1]
        ));
        $this->_xajaxMethods[] = $this->formatAttributeJs($name, $name, FE::TYPE_FUNCTION);
    }

    public function __get($attributeName)
    {
        return isset($this->_attributes[$attributeName]) ? $this->_attributes[$attributeName] : null;
    }
}
