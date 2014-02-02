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

    protected function _PrepareAttributes_JS ()
    {
        $attributes = Array(
            $this->_FormatAttribute_JS('name', 'sName'),
            $this->_FormatAttribute_JS('label', 'sLabel'),
            $this->_FormatAttribute_JS('error', 'sError'),
            $this->_FormatAttribute_JS('clickable', 'bClickable'),
            $this->_FormatAttribute_JS('deletable', 'bDeletable'),
            $this->_FormatAttribute_JS('sortable', 'bSortable'),
            $this->_FormatAttribute_JS('addable', 'bAddable'),
            $this->_FormatAttribute_JS('items', 'oItems', FE::TYPE_OBJECT),
            $this->_FormatAttribute_JS('total', 'iTotal'),
            $this->_FormatAttribute_JS('onClick', 'fOnClick', FE::TYPE_FUNCTION),
            $this->_FormatAttribute_JS('onAdd', 'fOnAdd', FE::TYPE_FUNCTION),
            $this->_FormatAttribute_JS('onAfterAdd', 'fOnAfterAdd', FE::TYPE_FUNCTION),
            $this->_FormatAttribute_JS('onDelete', 'fOnDelete', FE::TYPE_FUNCTION),
            $this->_FormatAttribute_JS('onAfterDelete', 'fOnAfterDelete', FE::TYPE_FUNCTION),
            $this->_FormatAttribute_JS('onSaveOrder', 'fOnSaveOrder', FE::TYPE_FUNCTION),
            $this->_FormatAttribute_JS('active', 'sActive'),
            $this->_FormatAttribute_JS('add_item_prompt', 'sAddItemPrompt'),
            $this->_FormatAttribute_JS('delete_item_prompt', 'sDeleteItemPrompt'),
            $this->_FormatAttribute_JS('set', 'sSet'),
            $this->_FormatRepeatable_JS(),
            $this->_FormatRules_JS(),
            $this->_FormatDependency_JS(),
            $this->_FormatDefaults_JS()
        );
        return $attributes;
    }
}
