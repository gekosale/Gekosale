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
 * Class SortableList
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class SortableList extends Field implements ElementInterface
{
    public function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatAttributeJs('error', 'sError'),
            $this->formatAttributeJs('clickable', 'bClickable'),
            $this->formatAttributeJs('deletable', 'bDeletable'),
            $this->formatAttributeJs('sortable', 'bSortable'),
            $this->formatAttributeJs('addable', 'bAddable'),
            $this->formatAttributeJs('items', 'oItems', ElementInterface::TYPE_OBJECT),
            $this->formatAttributeJs('total', 'iTotal'),
            $this->formatAttributeJs('onClick', 'fOnClick', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('onAdd', 'fOnAdd', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('onAfterAdd', 'fOnAfterAdd', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('onDelete', 'fOnDelete', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('onAfterDelete', 'fOnAfterDelete', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('onSaveOrder', 'fOnSaveOrder', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('active', 'sActive'),
            $this->formatAttributeJs('add_item_prompt', 'sAddItemPrompt'),
            $this->formatAttributeJs('delete_item_prompt', 'sDeleteItemPrompt'),
            $this->formatAttributeJs('set', 'sSet'),
            $this->formatRepeatableJs(),
            $this->formatRulesJs(),
            $this->formatDependencyJs(),
            $this->formatDefaultsJs()
        );

        return $attributes;
    }
}
