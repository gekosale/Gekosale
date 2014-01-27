<?php

namespace FormEngine\Elements;

class FieldsetLanguage extends Fieldset
{

    public function __construct ($container, $attributes)
    {
        parent::__construct($attributes);
        $this->languages = $container->getParameter('languages');
        $this->_attributes['repeat_min'] = count($this->languages);
        $this->_attributes['repeat_max'] = count($this->languages);
    }

    protected function _FormatLanguages_JS ()
    {
        $options = Array();
        foreach ($this->languages as $id => $translation){
            $value = addslashes($id);
            $label = addslashes($translation);
            $flag = addslashes($id);
            $options[] = "{sValue: '{$value}', sLabel: '{$label}',sFlag: '{$flag}' }";
        }
        
        return 'aoLanguages: [' . implode(', ', $options) . ']';
    }

    protected function _PrepareAttributes_JS ()
    {
        $attributes = Array(
            $this->_FormatAttribute_JS('name', 'sName'),
            $this->_FormatAttribute_JS('label', 'sLabel'),
            $this->_FormatRepeatable_JS(),
            $this->_FormatDependency_JS(),
            $this->_FormatLanguages_JS(),
            'aoFields: [' . $this->_RenderChildren() . ']'
        );
        
        return $attributes;
    }
}
