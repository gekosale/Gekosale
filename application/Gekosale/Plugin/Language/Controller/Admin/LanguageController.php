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
namespace Gekosale\Plugin\Language\Controller\Admin;

use Gekosale\Core\Controller\AdminController;

/**
 * Class LanguageController
 *
 * @package Gekosale\Plugin\Language\Controller\Admin
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class LanguageController extends AdminController
{
    public function indexAction()
    {
        $datagrid = $this->getDataGrid();

        $this->getXajaxManager()->registerFunctions([
            'getLanguageForAjax' => [$datagrid, 'getData'],
            'doDeleteLanguage'   => [$datagrid, 'delete']
        ]);

        $datagrid->init();

        return Array(
            'datagrid_filter' => $datagrid->getFilterData()
        );
    }

    public function addAction()
    {
        $form = $this->getForm()->init();

        if ($form->isValid()) {

            $this->getRepository()->save($form->getSubmitValues());

            return $this->redirect($this->generateUrl('admin.language.index'));
        }

        return Array(
            'form' => $form
        );
    }

    public function editAction($id)
    {
        $populateData = $this->getRepository()->getPopulateData($id);
        $form         = $this->getForm()->init($populateData);

        if ($form->isValid()) {

            $this->getRepository()->save($form->getSubmitValues(), $id);

            return $this->redirect($this->generateUrl('admin.language.index'));
        }

        return Array(
            'form' => $form
        );
    }

    protected function getDataGrid()
    {
        return $this->get('language.datagrid');
    }

    protected function getRepository()
    {
        return $this->get('language.repository');
    }

    protected function getForm()
    {
        return $this->get('language.form');
    }
}
