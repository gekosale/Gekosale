<?php
namespace FormEngine\Elements;

class Hidden extends Field
{

    protected function _PrepareAttributes_JS()
    {
        $attributes = Array(
            $this->_FormatAttribute_JS('name', 'sName'),
            $this->_FormatDependency_JS(),
            $this->_FormatDefaults_JS()
        );

        return $attributes;
    }

}
