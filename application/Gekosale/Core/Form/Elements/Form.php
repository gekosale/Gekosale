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

    const FORMAT_GROUPED = 0;

    const FORMAT_FLAT = 1;

    const TABS_VERTICAL = 0;

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

        if (!isset($this->_attributes['class'])) {
            $this->_attributes['class'] = '';
        }
        if (!isset($this->_attributes['action'])) {
            $this->_attributes['action'] = '';
        }
        if (!isset($this->_attributes['method'])) {
            $this->_attributes['method'] = 'post';
        }
        if (!isset($this->_attributes['tabs'])) {
            $this->_attributes['tabs'] = self::TABS_VERTICAL;
        }
    }

    public function Render_JS()
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
								{$this->_RenderChildren()}
							],
							oValues: " . json_encode($this->GetValues()) . ",
							oErrors: " . json_encode($this->GetErrors()) . "
						});
					});
				/*]]>*/
			</script>
		";
    }

    public function Render_Static()
    {
    }

    public function getSubmitValues($flags = 0)
    {
        return $this->GetValues($flags);
    }

    public function getElementValue($element)
    {
        return $this->GetValue($element);
    }

    public function GetValues($flags = 0)
    {
        if ($flags & self::FORMAT_FLAT) {
            $values = Array();
            foreach ($this->fields as $field) {
                if (is_object($field)) {
                    if (is_subclass_of($field, 'FormEngine\Elements\Field')) {
                        $values = array_merge_recursive($values, Array(
                            $field->GetName() => $field->GetValue()
                        ));
                    }
                } else {
                    if (is_subclass_of($field, 'FormEngine\Elements\Field')) {
                        $values = array_merge_recursive($values, Array(
                            $field->GetName() => $field->GetValue()
                        ));
                    }
                }
            }

            return $values;
        } else {
            return $this->_Harvest(Array(
                $this,
                '_HarvestValues'
            ));
        }

        return Array();
    }

    public function GetErrors()
    {
        return $this->_Harvest(Array(
            $this,
            '_HarvestErrors'
        ));
    }

    public function GetValue($element)
    {
        foreach ($this->fields as $field) {
            if ($field->GetName() == $element) {
                return $field->GetValue();
            }
        }
    }

    public function GetFlags()
    {
        return $this->_flags;
    }

    public function Populate($value, $flags = 0)
    {
        if ($flags & self::FORMAT_FLAT) {
            return;
        } else {
            $this->_values = $this->_values + $value;
        }
        $this->_populatingWholeForm = true;
        parent::Populate($value);
        $this->_populatingWholeForm = false;
    }

    public function Validate($values = Array())
    {
        $values = $this->SubmittedData();

        if (!isset($values[$this->_attributes['name'] . '_submitted']) or !$values[$this->_attributes['name'] . '_submitted']) {
            return false;
        }
        $this->Populate($values);

        return parent::Validate();
    }

    public function SubmittedData()
    {
        return $_POST;
    }

    public function IsAction($actionName)
    {
        $actionName = '_Action_' . $actionName;

        return (isset($_POST[$actionName]) && ($_POST[$actionName] == '1'));
    }
}
