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
namespace Gekosale\Plugin\Deliverer\Controller\Admin;

use Gekosale\Core\Controller\AdminController;

/**
 * Class DelivererController
 *
 * @package Gekosale\Plugin\Deliverer\Controller\Admin
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class DelivererController extends AdminController
{

    public function indexAction()
    {
        $datagrid = $this->getDataGrid();

        $this->getXajaxManager()->registerFunctions([
            'getDelivererForAjax' => [$datagrid, 'getData'],
            'doDeleteDeliverer'   => [$datagrid, 'delete']
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

            return $this->redirect($this->generateUrl($this->getDefaultRoute()));
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

            return $this->redirect($this->generateUrl($this->getDefaultRoute()));
        }

        return Array(
            'form' => $form
        );
    }

    /**
     * Get DataGrid
     */
    protected function getDataGrid()
    {
        return $this->get('deliverer.datagrid');
    }

    /**
     * Get Repository
     */
    protected function getRepository()
    {
        return $this->get('deliverer.repository');
    }

    /**
     * Get Form
     */
    protected function getForm()
    {
        return $this->get('deliverer.form');
    }

    /**
     * Get default route
     *
     * @return string
     */
    protected function getDefaultRoute()
    {
        return 'admin.deliverer.index';
    }
}
