<?php

namespace FormEngine\Elements;

class Fieldset extends Container
{

    protected function _PrepareAttributes_JS ()
    {
        $attributes = Array(
            $this->_FormatAttribute_JS('name', 'sName'),
            $this->_FormatAttribute_JS('label', 'sLabel'),
            $this->_FormatAttribute_JS('class', 'sClass'),
            $this->_FormatDependency_JS(),
            'aoFields: [' . $this->_RenderChildren() . ']'
        );
        return $attributes;
    }
}
