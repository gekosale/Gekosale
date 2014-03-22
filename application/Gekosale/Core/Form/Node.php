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

    protected $attributes;

    protected $_renderMode;

    protected $_tabs;

    protected $_jsNodeName;

    protected $_xajaxMethods;

    protected static $_nextId = 0;

    public function __construct($attributes)
    {
        $this->_id           = self::$_nextId++;
        $this->attributes   = $attributes;
        $this->_renderMode   = 'Static';
        $this->_tabs         = '';
        $class               = explode('\\', get_class($this));
        $this->_jsNodeName   = 'GForm' . end($class);
        $this->form          = null;
        $this->parent        = null;
        $this->_xajaxMethods = Array();
    }

    public function render($mode = 'JS', $tabs = '')
    {
        $this->_tabs       = $tabs;
        $this->_renderMode = $mode;
        $renderFunction    = 'render' . $mode;
        $lines             = explode("\n", $this->$renderFunction());
        foreach ($lines as &$line) {
            $line = $this->_tabs . $line;
        }

        return implode("\n", $lines);
    }

    public function addRule($rule)
    {
        if (!isset($this->attributes['rules']) || !is_array($this->attributes['rules'])) {
            $this->attributes['rules'] = Array();
        }
        $this->attributes['rules'][] = $rule;
    }

    public function clearRules()
    {
        $this->attributes['rules'] = Array();
    }

    public function addFilter($filter)
    {
        if (!isset($this->attributes['filters']) || !is_array($this->attributes['filters'])) {
            $this->attributes['filters'] = Array();
        }
        $this->attributes['filters'][] = $filter;
    }

    public function setFilter($filter)
    {
        if (!isset($this->attributes['filters']) || !is_array($this->attributes['filters'])) {
            $this->attributes['filters'] = Array();
        }
        $this->attributes['filters'][] = $filter;
    }

    public function clearFilters()
    {
        $this->attributes['filters'] = Array();
    }

    public function addDependency($dependency)
    {
        if (!isset($this->attributes['dependencies']) || !is_array($this->attributes['dependencies'])) {
            $this->attributes['dependencies'] = Array();
        }
        $this->attributes['dependencies'][] = $dependency;
    }

    protected function filter($values)
    {
        if (!isset($this->attributes['filters']) || !is_array($this->attributes['filters'])) {
            return $values;
        }
        if (is_array($values)) {
            foreach ($values as &$value) {
                foreach ($this->attributes['filters'] as $filter) {
                    $value = $filter->filter($value);
                }
            }
        } else {
            foreach ($this->attributes['filters'] as $filter) {
                $values = $filter->filter($values);
            }
        }

        return $values;
    }

    public function getName()
    {
        return $this->attributes['name'];
    }

    protected function harvestValues($node, $levels)
    {
        $value = $node->getValue();
        foreach ($levels as $level) {
            if (isset($value[$level])) {
                $value = $value[$level];
            } else {
                return '';
            }
        }

        return $value;
    }

    protected function harvestErrors($node, $levels)
    {
        if (!isset($node->attributes['error'])) {
            return '';
        }
        $value = $node->attributes['error'];
        foreach ($levels as $level) {
            if (isset($value[$level])) {
                $value = $value[$level];
            } else {
                return '';
            }
        }

        return $value;
    }

    protected function harvest($action, $levelsCount = 0, $levels = Array())
    {
        if (isset($this->_children)) {
            $array = Array();
            foreach ($this->_children as $child) {
                $name = $child->getName();
                if (empty($name)) {
                    continue;
                }
                if ($this instanceof Elements\FieldsetRepeatable) {
                    $repetitions = $child->harvestRepetitions($levelsCount);
                    foreach ($repetitions as $repetition) {
                        $levelsCopy                = $levels + [$repetition];
                        $array[$repetition][$name] = $child->harvest($action, $levelsCount + 1, $levelsCopy);
                    }
                } else {
                    $array[$name] = $child->harvest($action, $levelsCount, $levels);
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

    protected function harvestRepetitions($level = 0)
    {
        if (isset($this->_children)) {
            $array = Array();
            foreach ($this->_children as $child) {
                array_push($array, $child->harvestRepetitions($level));
            }

            return array_unique($array);
        } else {
            $value       = $this->getValue();
            $repetitions = $this->extractRepetitions($value, $level);

            return array_unique($repetitions);
        }
    }

    protected function extractRepetitions($array, $targetLevel, $level = 0)
    {
        if ($targetLevel >= $level) {
            if (is_array($array)) {
                return array_keys($array);
            }

            return Array();
        }
        $repetitions = Array();
        foreach ($array as $value) {
            array_push($repetitions, $this->extractRepetitions($value, $targetLevel, $level + 1));
        }

        return $repetitions;
    }

    protected function formatAttributesJs($attributes)
    {
        $attributes       = array_merge($attributes, $this->prepareAutoAttributesJs());
        $attributesString = PHP_EOL;
        foreach ($attributes as $attribute) {
            if (!empty($attribute)) {
                $attributesString .= $this->_tabs . $attribute . ",\n";
            }
        }

        return substr($attributesString, 0, -2) . "\n";
    }

    protected function formatAttributeJs($attributeName, $name = null, $type = Elements\ElementInterface::TYPE_STRING)
    {
        if ($name == null) {
            if (!isset($this->attributes[$attributeName])) {
                if ($type == Elements\ElementInterface::TYPE_FUNCTION) {
                    return 'null';
                } elseif ($type == Elements\ElementInterface::TYPE_NUMBER) {
                    return '0';
                } elseif ($type == Elements\ElementInterface::TYPE_ARRAY) {
                    return '[]';
                } elseif ($type == Elements\ElementInterface::TYPE_OBJECT) {
                    return '{}';
                } elseif ($type == Elements\ElementInterface::TYPE_BOOLEAN) {
                    return 'false';
                }

                return '\'\'';
            }
            if ($type == Elements\ElementInterface::TYPE_FUNCTION) {
                return $this->attributes[$attributeName];
            } elseif ($type == Elements\ElementInterface::TYPE_NUMBER) {
                return $this->attributes[$attributeName];
            } elseif ($type == Elements\ElementInterface::TYPE_ARRAY) {
                return json_encode($this->attributes[$attributeName]);
            } elseif ($type == Elements\ElementInterface::TYPE_OBJECT) {
                return json_encode($this->attributes[$attributeName]);
            } elseif ($type == Elements\ElementInterface::TYPE_BOOLEAN) {
                return $this->attributes[$attributeName] ? 'true' : 'false';
            }

            return str_replace(Array(
                "\r\n",
                "\n"
            ), '\n', '\'' . addslashes($this->attributes[$attributeName]) . '\'');
        }
        if (!isset($this->attributes[$attributeName])) {
            return '';
        }
        $value = $this->attributes[$attributeName];
        if ($type == Elements\ElementInterface::TYPE_ARRAY) {
            return $name . ': ' . json_encode($value);
        } elseif ($type == Elements\ElementInterface::TYPE_OBJECT) {
            return $name . ': ' . json_encode($value);
        } elseif (is_array($value)) {
            foreach ($value as &$valuePart) {
                if ($type == Elements\ElementInterface::TYPE_FUNCTION) {
                    $valuePart = '' . ($valuePart) . '';
                } elseif ($type == Elements\ElementInterface::TYPE_NUMBER) {
                    $valuePart = '' . ($valuePart) . '';
                } else {
                    $valuePart = '\'' . addslashes($valuePart) . '\'';
                }
            }

            return str_replace("\n", '\n', $name . ': [' . implode(', ', $value) . ']');
        } else {
            if ($type == Elements\ElementInterface::TYPE_FUNCTION) {
                return $name . ': ' . ($value) . '';
            } elseif ($type == Elements\ElementInterface::TYPE_NUMBER) {
                return $name . ': ' . ($value) . '';
            } elseif ($type == Elements\ElementInterface::TYPE_BOOLEAN) {
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
        if ((isset($this->attributes['repeat_min']) && ($this->attributes['repeat_min'] != 1)) || (isset($this->attributes['repeat_max']) && ($this->attributes['repeat_max'] != 1))) {
            $min
                = (isset($this->attributes['repeat_min']) && is_numeric($this->attributes['repeat_min'])) ? $this->attributes['repeat_min'] : 1;
            $max
                = (isset($this->attributes['repeat_max']) && is_numeric($this->attributes['repeat_max'])) ? $this->attributes['repeat_max'] : 1;
            if (isset($this->attributes['repeat_max']) && ($this->attributes['repeat_max'] == Elements\ElementInterface::INFINITE)) {
                $max = 'GForm.INFINITE';
            }

            return "oRepeat: {iMin: {$min}, iMax: {$max}}";
        }

        return '';
    }

    protected function formatDependencyJs()
    {
        $dependencies = Array();
        if (isset($this->attributes['dependencies']) && is_array($this->attributes['dependencies'])) {
            foreach ($this->attributes['dependencies'] as $dependency) {
                $dependencies[] = $dependency->renderJs();
            }
        }
        if (count($dependencies)) {
            return 'agDependencies: [' . implode(', ', $dependencies) . ']';
        }

        return '';
    }

    protected function formatFactorJs($factor, $name)
    {
        return "{$name}: {$this->attributes[$factor]->render()}";
    }

    public function renderJs()
    {
        $render = "
			{fType: {$this->_jsNodeName},{$this->formatAttributesJs($this->prepareAttributesJs())}}
		";

        return $render;
    }

    protected function prepareAttributesJs()
    {
        return Array();
    }

    public function renderStatic()
    {
    }

    public function isValid()
    {
        return true;
    }

    protected function isIterated($array)
    {
        if (is_numeric(key($array)) || substr(key($array), 0, 4) == 'new-') {
            return true;
        }

        return false;
    }

    public function populate($value)
    {
    }

    protected function prepareAutoAttributesJs()
    {
        $attributes = Array();
        $attributes = array_merge($attributes, $this->_xajaxMethods);

        return $attributes;
    }

    protected function registerXajaxMethod($name, $callback)
    {
        $jsName                   = $name . '_' . $this->_id;
        $this->attributes[$name] = 'xajax_' . $jsName;
        App::getRegistry()->xajaxInterface->registerFunction(array(
            $jsName,
            $callback[0],
            $callback[1]
        ));
        $this->_xajaxMethods[] = $this->formatAttributeJs($name, $name, FE::TYPE_FUNCTION);
    }

    public function __get($attributeName)
    {
        return isset($this->attributes[$attributeName]) ? $this->attributes[$attributeName] : null;
    }
}
