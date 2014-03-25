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
namespace Gekosale\Plugin\Category\Controller\Admin;

use Gekosale\Core\Controller\AdminController;

/**
 * Class CategoryController
 *
 * @package Gekosale\Plugin\Category\Controller\Admin
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CategoryController extends AdminController
{
    public function indexAction()
    {
        $tree = $this->getTree()->init();

        $this->registerFunctions();

        return Array(
            'tree' => $tree
        );
    }

    public function editAction($id)
    {
        $this->registerFunctions();

        $tree         = $this->getTree()->init();
        $populateData = $this->getRepository()->getPopulateData($id);
        $form         = $this->getForm()->init($populateData);

        if ($form->isValid()) {

            $this->getRepository()->save($form->getSubmitValues(), $id);

            return $this->redirect($this->generateUrl($this->getDefaultRoute()));
        }

        return Array(
            'tree' => $tree,
            'form' => $form
        );
    }

    /**
     * Get Repository
     */
    protected function getRepository()
    {
        return $this->get('category.repository');
    }

    /**
     * Get Form
     */
    protected function getForm()
    {
        return $this->get('category.form');
    }

    /**
     * Get Tree
     */
    protected function getTree()
    {
        return $this->get('category.tree');
    }

    /**
     * Get default route
     *
     * @return string
     */
    protected function getDefaultRoute()
    {
        return 'admin.category.index';
    }

    /**
     * Registers all needed xajax functions
     */
    protected function registerFunctions()
    {
        $this->getXajax()->registerFunction(Array(
            'DuplicateCategory',
            $this->getRepository(),
            'duplicateCategory'
        ));

        $this->getXajax()->registerFunction([
            'doAJAXRefreshSeoCategory',
            $this->getRepository(),
            'doAJAXRefreshSeoCategory'
        ]);

        $this->getXajaxManager()->registerFunction([
            'AddCategory',
            $this->getRepository(),
            'quickAddCategory'
        ]);

        $this->getXajaxManager()->registerFunction([
            'DeleteCategory',
            $this->getRepository(),
            'delete'
        ]);

        $this->getXajaxManager()->registerFunction([
            'ChangeCategoryOrder',
            $this->getRepository(),
            'changeCategoryOrder'
        ]);

        $this->getXajaxManager()->registerFunctions([
            'doAJAXCreateSeoCategory' => [$this->getRepository(), 'doAJAXCreateSeoCategory'],
        ]);
    }
}
