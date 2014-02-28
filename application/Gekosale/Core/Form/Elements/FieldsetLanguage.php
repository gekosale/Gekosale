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

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FieldsetLanguage
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class FieldsetLanguage extends Fieldset implements ElementInterface
{

    protected $languages = Array();

    public function __construct(ContainerInterface $container, $attributes)
    {
        parent::__construct($attributes);

        $this->languages = $attributes['languages'];
        $count           = count($this->languages);

        $this->_attributes['repeat_min'] = $count;
        $this->_attributes['repeat_max'] = $count;
    }

    protected function _FormatLanguages_JS()
    {

        $options = Array();
        foreach ($this->languages as $language) {
            $value     = addslashes($language['id']);
            $label     = addslashes($language['translation']);
            $flag      = addslashes($language['flag']);
            $options[] = "{sValue: '{$value}', sLabel: '{$label}',sFlag: '{$flag}' }";
        }

        return 'aoLanguages: [' . implode(', ', $options) . ']';
    }

    protected function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatRepeatableJs(),
            $this->formatDependencyJs(),
            $this->_FormatLanguages_JS(),
            'aoFields: [' . $this->_RenderChildren() . ']'
        );

        return $attributes;
    }

}
