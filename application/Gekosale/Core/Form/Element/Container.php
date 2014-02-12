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

abstract class Container extends Node
{

    protected $_children;

    protected $_tabsOffset;

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        $this->_children = Array();
        $this->_tabsOffset = '';
    }

    public function AddChild ($child)
    {
        $this->_children[] = $child;
        $child->form = $this->form;
        $child->parent = $this;
        $childName = $child->getName();
        if (isset($this->form->fields[$childName])) {
            if (is_array($this->form->fields[$childName])) {
                $this->form->fields[$childName][] = $child;
            }
            else {
                $this->form->fields[$childName] = Array(
                    $this->form->fields[$childName],
                    $child
                );
            }
        }
        else {
            $this->form->fields[$childName] = $child;
        }
        return $child;
    }

    public function AddFieldset ($options, $container = NULL)
    {
        return $this->AddChild(new Fieldset($options, $container));
    }

    public function AddFieldsetLanguage ($options, $container = NULL)
    {
        return $this->AddChild(new FieldsetLanguage($options, $container));
    }

    public function addTextField ($options, $container = NULL)
    {
        return $this->AddChild(new TextField($options, $container));
    }

    public function addRule ($rule)
    {
        foreach ($this->_children as $child) {
            $child->addRule($rule);
        }
    }

    public function addRuleRequired ($options)
    {
        return $this->addRule(new \Gekosale\Core\Form\Rule\Required($options));
    }

    public function clearRules ()
    {
        foreach ($this->_children as $child) {
            $child->clearRules();
        }
    }

    public function addFilter ($filter)
    {
        foreach ($this->_children as $child) {
            $child->addFilter($filter);
        }
    }

    public function clearFilters ()
    {
        foreach ($this->_children as $child) {
            $child->clearFilters();
        }
    }

    public function populate ($value)
    {
        if (isset($value) && is_array($value) && $this->_IsIterated($value)) {
            foreach ($this->_children as $child) {
                $valueArray = Array();
                if (isset($value) && is_array($value)) {
                    foreach ($value as $i => $repetition) {
                        $name = $child->getName();
                        if (! empty($name)) {
                            if (isset($repetition[$name])) {
                                $valueArray[$i] = $repetition[$name];
                            }
                            else {
                                $valueArray[$i] = '';
                            }
                        }
                    }
                }
                
                $child->populate($valueArray);
            }
        }
        else { // simple value
            foreach ($this->_children as $child) {
                $name = $child->getName();
                if (empty($name)) {
                    continue;
                }
                if (isset($value[$name])) {
                    $child->populate($value[$name]);
                }
                elseif ($this->form->_populatingWholeForm) {
                    $child->populate(null);
                }
            }
        }
    }

    protected function renderChildren  ()
    {
        $render = Array();
        foreach ($this->_children as $child) {
            $render[] = $child->render($this->_renderMode, $this->_tabs . $this->_tabsOffset);
        }
        return implode(',', $render);
    }

    public function validate ()
    {
        $result = true;
        foreach ($this->_children as $child) {
            if (! $child->validate()) {
                $result = false;
            }
        }
        return $result;
    }

    protected function _getValues ()
    {
        $values = Array();
        foreach ($this->_children as $child) {
            if (is_subclass_of($child, 'FormEngine\Elements\Container')) {
                $values[$child->getName()] = $child->_getValues();
            }
            elseif (is_subclass_of($child, 'FormEngine\Elements\Field')) {
                $values[$child->getName()] = $child->getValue();
            }
        }
        return $values;
    }
}
