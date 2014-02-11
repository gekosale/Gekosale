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

class SortableList extends Field
{

    protected $_jsGetChildren;

    public function __construct ($attributes)
    {
        parent::__construct($attributes);
    }

    protected function prepareAttributesJavascript ()
    {
        $attributes = Array(
            $this->formatAttributeJavascript('name', 'sName'),
            $this->formatAttributeJavascript('label', 'sLabel'),
            $this->formatAttributeJavascript('error', 'sError'),
            $this->formatAttributeJavascript('clickable', 'bClickable'),
            $this->formatAttributeJavascript('deletable', 'bDeletable'),
            $this->formatAttributeJavascript('sortable', 'bSortable'),
            $this->formatAttributeJavascript('addable', 'bAddable'),
            $this->formatAttributeJavascript('items', 'oItems', FE::TYPE_OBJECT),
            $this->formatAttributeJavascript('total', 'iTotal'),
            $this->formatAttributeJavascript('onClick', 'fOnClick', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('onAdd', 'fOnAdd', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('onAfterAdd', 'fOnAfterAdd', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('onDelete', 'fOnDelete', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('onAfterDelete', 'fOnAfterDelete', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('onSaveOrder', 'fOnSaveOrder', FE::TYPE_FUNCTION),
            $this->formatAttributeJavascript('active', 'sActive'),
            $this->formatAttributeJavascript('add_item_prompt', 'sAddItemPrompt'),
            $this->formatAttributeJavascript('delete_item_prompt', 'sDeleteItemPrompt'),
            $this->formatAttributeJavascript('set', 'sSet'),
            $this->formatRepeatableJavascript(),
            $this->_FormatRules_JS(),
            $this->formatDependencyJavascript(),
            $this->_FormatDefaults_JS()
        );
        return $attributes;
    }
}
