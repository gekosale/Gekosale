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

class FieldsetLanguage extends Fieldset
{

    public function __construct ($attributes, ContainerInterface $container)
    {
        parent::__construct($attributes);
        $this->languages = $container->getParameter('locales');
        $this->_attributes['repeat_min'] = count($this->languages);
        $this->_attributes['repeat_max'] = count($this->languages);
    }

    protected function _FormatLanguages_JS ()
    {
        $options = Array();
        foreach ($this->languages as $id => $name) {
            $value = addslashes($id);
            $label = addslashes($name);
            $flag = addslashes($id) . '.png';
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
