<?php

namespace FormEngine\Elements;

class FieldsetRepeatable extends Fieldset
{

    protected function _PrepareAttributes_JS ()
    {
        $attributes = Array(
            $this->_FormatAttribute_JS('name', 'sName'),
            $this->_FormatAttribute_JS('label', 'sLabel'),
            $this->_FormatRepeatable_JS(),
            $this->_FormatDependency_JS(),
            'aoFields: [' . $this->_RenderChildren() . ']'
        );
        return $attributes;
    }
}
