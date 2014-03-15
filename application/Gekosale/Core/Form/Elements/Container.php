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

use Gekosale\Core\Form\Filters\FilterInterface;
use Gekosale\Core\Form\Node;

/**
 * Class Container
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>s
 */
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

    public function addChild($child)
    {
        $this->_children[] = $child;
        $child->form       = $this->form;
        $child->parent     = $this;
        $childName         = $child->getName();
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

    final public function addChildren($children)
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    public function addRule($rule)
    {
        foreach ($this->_children as $child) {
            $child->addRule($rule);
        }
    }

    public function clearRules()
    {
        foreach ($this->_children as $child) {
            $child->clearRules();
        }
    }

    public function addFilter($filter)
    {
        foreach ($this->_children as $child) {
            $child->addFilter($filter);
        }
    }

    public function addFilters(array $filters)
    {
        foreach ($filters as $filter) {
            if (!$filter instanceof FilterInterface) {
                throw new \LogicException('Filter must implement FilterInterface');
            }
            $this->addFilter($filter);
        }
    }

    public function clearFilters()
    {
        foreach ($this->_children as $child) {
            $child->clearFilters();
        }
    }

    public function populate($value)
    {
        if (isset($value) && is_array($value) && $this->isIterated($value)) {
            foreach ($this->_children as $child) {
                $valueArray = Array();
                if (isset($value) && is_array($value)) {
                    foreach ($value as $i => $repetition) {
                        $name = $child->getName();
                        if (!empty($name)) {
                            if (isset($repetition[$name])) {
                                $valueArray[$i] = $repetition[$name];
                            } else {
                                $valueArray[$i] = '';
                            }
                        }
                    }
                }
                $child->populate($valueArray);
            }
        } else { // simple value
            foreach ($this->_children as $child) {
                $name = $child->getName();
                if (empty($name)) {
                    continue;
                }
                if (isset($value[$name])) {
                    $child->populate($value[$name]);
                } elseif ($this->form->_populatingWholeForm) {
                    $child->populate(null);
                }
            }
        }
    }

    protected function renderChildren()
    {
        $render = Array();
        foreach ($this->_children as $child) {
            $render[] = $child->render($this->_renderMode, $this->_tabs . $this->_tabsOffset);
        }

        return implode(',', $render);
    }

    public function isValid($values = Array())
    {
        $result = true;
        foreach ($this->_children as $child) {
            if (!$child->isValid()) {
                $result = false;
            }
        }

        return $result;
    }

    protected function getValues()
    {
        $values = Array();
        foreach ($this->_children as $child) {
            if ($child instanceof Container) {
                $values[$child->getName()] = $child->getValues();
            } elseif ($child instanceof Field) {
                $values[$child->getName()] = $child->getValue();
            }
        }

        return $values;
    }
}
