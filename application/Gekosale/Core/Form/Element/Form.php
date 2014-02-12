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

use Symfony\Component\DependencyInjection\ContainerInterface;

class Form extends Container
{

    const FORMAT_GROUPED = 0;

    const FORMAT_FLAT = 1;

    const TABS_VERTICAL = 0;

    const TABS_HORIZONTAL = 1;

    public $fields;

    protected $_values;

    protected $_flags;

    protected $_container;

    protected $_populatingWholeForm;

    public function __construct ($attributes, ContainerInterface $container)
    {
        parent::__construct($attributes, $container);
        $this->_populatingWholeForm = false;
        $this->_container = $container;
        $this->fields = Array();
        $this->_values = Array();
        $this->_flags = Array();
        $this->form = $this;
        
        if (! isset($this->_attributes['method'])) {
            $this->_attributes['method'] = 'POST';
        }
        
        if (! isset($this->_attributes['action'])) {
            $this->_attributes['action'] = '';
        }
        
        if (! isset($this->_attributes['class'])) {
            $this->_attributes['class'] = '';
        }
        
        if (! isset($this->_attributes['tabs'])) {
            $this->_attributes['tabs'] = self::TABS_VERTICAL;
        }
    }

    public function renderJavascript ()
    {
        return "
			<form id=\"{$this->_attributes['name']}\" method=\"{$this->_attributes['method']}\" action=\"{$this->_attributes['action']}\">
				<input type=\"hidden\" name=\"{$this->_attributes['name']}_submitted\" value=\"1\"/>
			</form>
			<script type=\"text/javascript\">
				/*<![CDATA[*/
					GCore.OnLoad(function() {
						$('#{$this->_attributes['name']}').GForm({
							sFormName: '{$this->_attributes['name']}',
							sClass: '{$this->_attributes['class']}',
							iTabs: " . (($this->_attributes['tabs'] == self::TABS_VERTICAL) ? 'GForm.TABS_VERTICAL' : 'GForm.TABS_HORIZONTAL') . ",
							aoFields: [
								{$this->renderChildren ()}
							],
							oValues: " . json_encode($this->getValues()) . ",
							oErrors: " . json_encode($this->getErrors ()) . "
						});
					});
				/*]]>*/
			</script>
		";
    }

    public function renderStatic ()
    {
    }

    public function getSubmitValues ($flags = 1)
    {
        return $this->getValues($flags);
    }

    public function getElementValue ($element)
    {
        return $this->getValue($element);
    }

    public function getValues ($flags = 0)
    {
        if ($flags & self::FORMAT_FLAT) {
            $values = Array();
            foreach ($this->fields as $field) {
                if (is_object($field)) {
                    if ($field instanceof Field) {
                        $values = array_merge_recursive($values, Array(
                            $field->getName() => $field->getValue()
                        ));
                    }
                }
                else {
                    if ($field instanceof Field) {
                        $values = array_merge_recursive($values, Array(
                            $field->getName() => $field->getValue()
                        ));
                    }
                }
            }
            
            return $values;
        }
        else {
            return $this->harvest(Array(
                $this,
                'harvestValues'
            ));
        }
        
        return Array();
    }

    public function getErrors  ()
    {
        return $this->harvest(Array(
            $this,
            'harvestErrors'
        ));
    }

    public function getValue ($element)
    {
        foreach ($this->fields as $field) {
            if ($field->getName() == $element) {
                return $field->getValue();
            }
        }
    }

    public function getFlags ()
    {
        return $this->_flags;
    }

    public function populate ($value, $flags = 0)
    {
        if ($flags & self::FORMAT_FLAT) {
            return;
        }
        else {
            $this->_values = $this->_values + $value;
        }
        $this->_populatingWholeForm = true;
        parent::populate($value);
        $this->_populatingWholeForm = false;
    }

    public function getSubmittedData ()
    {
        return $_POST;
    }

    public function isAction ($actionName)
    {
        $actionName = '_Action_' . $actionName;
        return (isset($_POST[$actionName]) && ($_POST[$actionName] == '1'));
    }

    public function validate ()
    {
        $values = $this->getSubmittedData();
        if (! isset($values[$this->_attributes['name'] . '_submitted']) || ! $values[$this->_attributes['name'] . '_submitted']) {
            return false;
        }
        $this->populate($values);
        
        return parent::validate();
    }
}
