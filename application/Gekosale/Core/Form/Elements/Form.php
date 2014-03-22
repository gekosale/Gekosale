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

/**
 * Class Form
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Form extends Container
{

    const FORMAT_GROUPED  = 0;
    const FORMAT_FLAT     = 1;
    const TABS_VERTICAL   = 0;
    const TABS_HORIZONTAL = 1;

    public $fields;

    protected $_values;

    protected $_flags;

    protected $_populatingWholeForm;

    public function __construct($attributes)
    {
        parent::__construct($attributes);
        $this->_populatingWholeForm = false;
        $this->fields               = Array();
        $this->_values              = Array();
        $this->_flags               = Array();
        $this->form                 = $this;

        if (!isset($this->attributes['class'])) {
            $this->attributes['class'] = '';
        }
        if (!isset($this->attributes['action'])) {
            $this->attributes['action'] = '';
        }
        if (!isset($this->attributes['method'])) {
            $this->attributes['method'] = 'post';
        }
        if (!isset($this->attributes['tabs'])) {
            $this->attributes['tabs'] = self::TABS_VERTICAL;
        }
    }

    public function renderJs()
    {
        return "
			<form id=\"{$this->attributes['name']}\" method=\"{$this->attributes['method']}\" action=\"{$this->attributes['action']}\">
				<input type=\"hidden\" name=\"{$this->attributes['name']}_submitted\" value=\"1\"/>
			</form>
			<script type=\"text/javascript\">
				/*<![CDATA[*/
					GCore.OnLoad(function() {
						$('#{$this->attributes['name']}').GForm({
							sFormName: '{$this->attributes['name']}',
							sClass: '{$this->attributes['class']}',
							iTabs: " . (($this->attributes['tabs'] == self::TABS_VERTICAL) ? 'GForm.TABS_VERTICAL' : 'GForm.TABS_HORIZONTAL') . ",
							aoFields: [
								{$this->renderChildren()}
							],
							oValues: " . json_encode($this->getValues()) . ",
							oErrors: " . json_encode($this->getErrors()) . "
						});
					});
				/*]]>*/
			</script>
		";
    }

    public function renderStatic()
    {
    }

    public function getSubmitValuesFlat()
    {
        return $this->getValues(self::FORMAT_FLAT);
    }

    public function getSubmitValuesGrouped()
    {
        return $this->getValues(self::FORMAT_GROUPED);
    }

    public function getElementValue($element)
    {
        return $this->getValue($element);
    }

    public function getValues($flags = 0)
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
                } else {
                    if ($field instanceof Field) {
                        $values = array_merge_recursive($values, Array(
                            $field->getName() => $field->getValue()
                        ));
                    }
                }
            }

            return $values;
        } else {
            return $this->harvest(Array(
                $this,
                'harvestValues'
            ));
        }

        return Array();
    }

    public function getErrors()
    {
        return $this->harvest(Array(
            $this,
            'harvestErrors'
        ));
    }

    public function getValue($element)
    {
        foreach ($this->fields as $field) {
            if ($field->getName() == $element) {
                return $field->getValue();
            }
        }
    }

    public function GetFlags()
    {
        return $this->_flags;
    }

    public function populate($value, $flags = 0)
    {
        if ($flags & self::FORMAT_FLAT) {
            return;
        } else {
            $this->_values = $this->_values + $value;
        }
        $this->_populatingWholeForm = true;
        parent::populate($value);
        $this->_populatingWholeForm = false;
    }

    public function isValid($values = Array())
    {
        $values = $this->getSubmittedData();

        if (!isset($values[$this->attributes['name'] . '_submitted']) || !$values[$this->attributes['name'] . '_submitted']) {
            return false;
        }
        $this->populate($values);

        return parent::isValid();
    }

    public function getSubmittedData()
    {
        return $_POST;
    }

    public function isAction($actionName)
    {
        $actionName = '_Action_' . $actionName;

        return (isset($_POST[$actionName]) && ($_POST[$actionName] == '1'));
    }
}
