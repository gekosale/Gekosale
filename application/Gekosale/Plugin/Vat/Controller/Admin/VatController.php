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
namespace Gekosale\Plugin\Vat\Controller\Admin;

use Gekosale\Core\Controller\AdminController;

/**
 * Class VatController
 *
 * @package Gekosale\Plugin\Vat\Controller\Admin
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class VatController extends AdminController
{

    public function indexAction()
    {
        $datagrid = $this->getDataGrid();

        $this->getXajaxManager()->registerFunctions([
            'getVatForAjax' => [$datagrid, 'getData'],
            'doDeleteVat'   => [$datagrid, 'delete']
        ]);

        $datagrid->init();

        return Array(
            'datagrid_filter' => $datagrid->getFilterData()
        );
    }

    public function addAction()
    {
        $form = $this->getForm()->init();

        if ($form->Validate()) {

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

        if ($form->Validate()) {

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
        return $this->get('vat.datagrid');
    }

    /**
     * Get Repository
     */
    protected function getRepository()
    {
        return $this->get('vat.repository');
    }

    /**
     * Get Form
     */
    protected function getForm()
    {
        return $this->get('vat.form');
    }

    /**
     * Get default route
     *
     * @return string
     */
    protected function getDefaultRoute()
    {
        return 'admin.vat.index';
    }
}
