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

abstract class Container extends Node
{

    protected $_children;
    protected $_tabsOffset;

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        $this->_children   = Array();
        $this->_tabsOffset = '';
    }

    public function AddChild($child)
    {
        $this->_children[] = $child;
        $child->form       = $this->form;
        $child->parent     = $this;
        $childName         = $child->GetName();
        if (isset($this->form->fields[$childName])) {
            if (is_array($this->form->fields[$childName])) {
                $this->form->fields[$childName][] = $child;
            } else {
                $this->form->fields[$childName] = Array(
                    $this->form->fields[$childName],
                    $child
                );
            }
        } else {
            $this->form->fields[$childName] = $child;
        }

        return $child;
    }

    final public function AddChildren($children)
    {
        foreach ($children as $child) {
            $this->AddChild($child);
        }
    }

    public function AddRule($rule)
    {
        foreach ($this->_children as $child) {
            $child->AddRule($rule);
        }
    }

    public function ClearRules()
    {
        foreach ($this->_children as $child) {
            $child->ClearRules();
        }
    }

    public function AddFilter($filter)
    {
        foreach ($this->_children as $child) {
            $child->AddFilter($filter);
        }
    }

    public function ClearFilters()
    {
        foreach ($this->_children as $child) {
            $child->ClearFilters();
        }
    }

    public function Populate($value)
    {
        if (isset($value) && is_array($value) && $this->_IsIterated($value)) { // iterated
            // value
            foreach ($this->_children as $child) {
                $valueArray = Array();
                if (isset($value) && is_array($value)) {
                    foreach ($value as $i => $repetition) {
                        $name = $child->GetName();
                        if (!empty($name)) {
                            if (isset($repetition[$name])) {
                                $valueArray[$i] = $repetition[$name];
                            } else {
                                $valueArray[$i] = '';
                            }
                        }
                    }
                }
                $child->Populate($valueArray);
            }
        } else { // simple value
            foreach ($this->_children as $child) {
                $name = $child->GetName();
                if (empty($name)) {
                    continue;
                }
                if (isset($value[$name])) {
                    $child->Populate($value[$name]);
                } elseif ($this->form->_populatingWholeForm) {
                    $child->Populate(null);
                }
            }
        }
    }

    protected function _RenderChildren()
    {
        $render = Array();
        foreach ($this->_children as $child) {
            $render[] = $child->Render($this->_renderMode, $this->_tabs . $this->_tabsOffset);
        }

        return implode(',', $render);
    }

    public function Validate($values = Array())
    {
        $result = true;
        foreach ($this->_children as $child) {
            if (!$child->Validate()) {
                $result = false;
            }
        }

        return $result;
    }

    protected function _GetValues()
    {
        $values = Array();
        foreach ($this->_children as $child) {
            if (is_subclass_of($child, 'FormEngine\Elements\Container')) {
                $values[$child->GetName()] = $child->_GetValues();
            } elseif (is_subclass_of($child, 'FormEngine\Elements\Field')) {
                $values[$child->GetName()] = $child->GetValue();
            }
        }

        return $values;
    }
}
