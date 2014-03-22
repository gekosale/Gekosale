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
 * Class Tree
 *
 * @package Gekosale\Core\Form\Elements
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Tree extends Field implements ElementInterface
{

    protected $jsGetChildren;
    protected $container;

    public function __construct($attributes, ContainerInterface $container)
    {
        parent::__construct($attributes);
        $this->container      = $container;
        $this->_jsGetChildren = 'GetChildren_' . $this->_id;

        if (!isset($this->attributes['retractable'])) {
            $this->attributes['retractable'] = true;
        }

        $this->attributes['total'] = count($this->attributes['items']);
    }

    public function prepareAttributesJs()
    {
        $attributes = Array(
            $this->formatAttributeJs('name', 'sName'),
            $this->formatAttributeJs('label', 'sLabel'),
            $this->formatAttributeJs('addLabel', 'sAddLabel'),
            $this->formatAttributeJs('error', 'sError'),
            $this->formatAttributeJs('selectable', 'bSelectable'),
            $this->formatAttributeJs('choosable', 'bChoosable'),
            $this->formatAttributeJs('clickable', 'bClickable'),
            $this->formatAttributeJs('deletable', 'bDeletable'),
            $this->formatAttributeJs('sortable', 'bSortable'),
            $this->formatAttributeJs('retractable', 'bRetractable'),
            $this->formatAttributeJs('addable', 'bAddable'),
            $this->formatAttributeJs('total', 'iTotal'),
            $this->formatAttributeJs('restrict', 'iRestrict'),
            $this->formatAttributeJs('items', 'oItems', ElementInterface::TYPE_OBJECT),
            $this->formatAttributeJs('onClick', 'fOnClick', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('onDuplicate', 'fOnDuplicate', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('onAdd', 'fOnAdd', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('onAfterAdd', 'fOnAfterAdd', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('onDelete', 'fOnDelete', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('onAfterDelete', 'fOnAfterDelete', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('onSaveOrder', 'fOnSaveOrder', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('active', 'sActive'),
            $this->formatAttributeJs('onAfterDeleteId', 'sOnAfterDeleteId'),
            $this->formatAttributeJs('add_item_prompt', 'sAddItemPrompt'),
            $this->formatAttributeJs('get_children', 'fGetChildren', ElementInterface::TYPE_FUNCTION),
            $this->formatAttributeJs('prevent_duplicates', 'bPreventDuplicates', ElementInterface::TYPE_BOOLEAN),
            $this->formatAttributeJs('prevent_duplicates_on_all_levels', 'bPreventDuplicatesOnAllLevels', ElementInterface::TYPE_BOOLEAN),
            $this->formatAttributeJs('set', 'sSet'),
            $this->formatRepeatableJs(),
            $this->formatRulesJs(),
            $this->formatDependencyJs(),
            $this->formatDefaultsJs()
        );

        return $attributes;
    }
}
