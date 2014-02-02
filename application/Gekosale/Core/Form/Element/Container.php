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
        $childName = $child->GetName();
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

    public function AddTextField ($options, $container = NULL)
    {
        return $this->AddChild(new TextField($options, $container));
    }

    public function AddRule ($rule)
    {
        foreach ($this->_children as $child) {
            $child->AddRule($rule);
        }
    }

    public function AddRuleRequired ($options)
    {
        return $this->AddRule(new \Gekosale\Core\Form\Rule\Required($options));
    }

    public function ClearRules ()
    {
        foreach ($this->_children as $child) {
            $child->ClearRules();
        }
    }

    public function AddFilter ($filter)
    {
        foreach ($this->_children as $child) {
            $child->AddFilter($filter);
        }
    }

    public function ClearFilters ()
    {
        foreach ($this->_children as $child) {
            $child->ClearFilters();
        }
    }

    public function Populate ($value)
    {
        if (isset($value) && is_array($value) && $this->_IsIterated($value)) {
            foreach ($this->_children as $child) {
                $valueArray = Array();
                if (isset($value) && is_array($value)) {
                    foreach ($value as $i => $repetition) {
                        $name = $child->GetName();
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
                
                $child->Populate($valueArray);
            }
        }
        else { // simple value
            foreach ($this->_children as $child) {
                $name = $child->GetName();
                if (empty($name)) {
                    continue;
                }
                if (isset($value[$name])) {
                    $child->Populate($value[$name]);
                }
                elseif ($this->form->_populatingWholeForm) {
                    $child->Populate(null);
                }
            }
        }
    }

    protected function _RenderChildren ()
    {
        $render = Array();
        foreach ($this->_children as $child) {
            $render[] = $child->Render($this->_renderMode, $this->_tabs . $this->_tabsOffset);
        }
        return implode(',', $render);
    }

    public function Validate ()
    {
        $result = true;
        foreach ($this->_children as $child) {
            if (! $child->Validate()) {
                $result = false;
            }
        }
        return $result;
    }

    protected function _GetValues ()
    {
        $values = Array();
        foreach ($this->_children as $child) {
            if (is_subclass_of($child, 'FormEngine\Elements\Container')) {
                $values[$child->GetName()] = $child->_GetValues();
            }
            elseif (is_subclass_of($child, 'FormEngine\Elements\Field')) {
                $values[$child->GetName()] = $child->GetValue();
            }
        }
        return $values;
    }
}
