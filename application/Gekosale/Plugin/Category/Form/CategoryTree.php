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
namespace Gekosale\Plugin\Category\Form;

use Gekosale\Core\Form;
use Gekosale\Plugin\Category\Event\CategoryFormEvent;

/**
 * Class CategoryTree
 *
 * @package Gekosale\Plugin\Category\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CategoryTree extends Form
{

    public function init($categoryData = Array())
    {
        $form = $this->addForm([
            'name'  => 'category_tree',
            'class' => 'category-select',
        ]);

        $form->addChild($this->addTree([
            'name'               => 'categories',
            'label'              => $this->trans('Categories'),
            'add_item_prompt'    => $this->trans('Category name'),
            'addLabel'           => $this->trans('Add category'),
            'sortable'           => true,
            'selectable'         => false,
            'clickable'          => true,
            'deletable'          => true,
            'addable'            => true,
            'prevent_duplicates' => true,
            'items'              => $this->get('category.repository')->getCategoriesTree(),
            'onClick'            => 'openCategoryEditor',
            'onDuplicate'        => 'xajax_DuplicateCategory',
            'onSaveOrder'        => 'xajax_ChangeCategoryOrder',
            'onAdd'              => 'xajax_AddCategory',
            'onAfterAdd'         => 'openCategoryEditor',
            'onDelete'           => 'xajax_DeleteCategory',
            'onAfterDelete'      => 'openCategoryEditor',
            //            'onAfterDeleteId'    => $categoryData['next'],
            'active'             => $this->getParam('id')
        ]));

        $form->AddFilter($this->addFilterNoCode());
        $form->AddFilter($this->addFilterSecure());

        $event = new CategoryFormEvent($form, $categoryData);

        $this->getDispatcher()->dispatch(CategoryFormEvent::TREE_INIT_EVENT, $event);

        $form->Populate($event->getPopulateData());

        return $form;
    }
}
