<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the
 * Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it
 * through the
 * world-wide-web, please send an email to license@verison.pl so we can send you
 * a copy immediately.
 */
namespace FormEngine\Elements;

use Gekosale\App as App;
use Gekosale\Translation;
use FormEngine\Rules;

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

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
        $this->_populatingWholeForm = false;
        $this->fields = Array();
        $this->_values = Array();
        $this->_flags = Array();
        $this->form = $this;

        if (! isset($this->_attributes['class'])){
            $this->_attributes['class'] = '';
        }
        if (! isset($this->_attributes['action'])){
            $this->_attributes['action'] = '';
        }
        if (! isset($this->_attributes['method'])){
            $this->_attributes['method'] = 'post';
        }
        if (! isset($this->_attributes['tabs'])){
            $this->_attributes['tabs'] = self::TABS_VERTICAL;
        }
    }

    public function Render_JS ()
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

    public function Render_Static ()
    {
    }

    public function getSubmitValues ($flags = 0)
    {
        return $this->GetValues($flags);
    }

    public function getElementValue ($element)
    {
        return $this->GetValue($element);
    }

    public function GetValues ($flags = 0)
    {
        if ($flags & self::FORMAT_FLAT){
            $values = Array();
            foreach ($this->fields as $field){
                if (is_object($field)){
                    if (is_subclass_of($field, 'FormEngine\Elements\Field')){
                        $values = array_merge_recursive($values, Array(
                            $field->GetName() => $field->GetValue()
                        ));
                    }
                }
                else
                    if (is_subclass_of($field, 'FormEngine\Elements\Field')){
                        $values = array_merge_recursive($values, Array(
                            $field->GetName() => $field->GetValue()
                        ));
                    }
            }
            return $values;
        }
        else{
            return $this->_Harvest(Array(
                $this,
                '_HarvestValues'
            ));
        }
        return Array();
    }

    public function GetErrors ()
    {
        return $this->_Harvest(Array(
            $this,
            '_HarvestErrors'
        ));
    }

    public function GetValue ($element)
    {
        foreach ($this->fields as $field){
            if ($field->GetName() == $element){
                return $field->GetValue();
            }
        }
    }

    public function GetFlags ()
    {
        return $this->_flags;
    }

    public function Populate ($value, $flags = 0)
    {
        if ($flags & self::FORMAT_FLAT){
            return;
        }
        else{
            $this->_values = $this->_values + $value;
        }
        $this->_populatingWholeForm = true;
        parent::Populate($value);
        $this->_populatingWholeForm = false;
    }

    public function Validate ($values = Array())
    {
        $values = $_POST;

        if (! isset($values[$this->_attributes['name'] . '_submitted']) or ! $values[$this->_attributes['name'] . '_submitted']){
            return false;
        }
        $this->Populate($values);
        return parent::Validate();
    }
}
