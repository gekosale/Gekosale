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
namespace Gekosale\Plugin\Availability\Controller\Admin;

use Gekosale\Core\Controller\AdminController;

/**
 * Class AvailabilityController
 *
 * @package Gekosale\Plugin\Availability\Controller\Admin
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class AvailabilityController extends AdminController
{

    public function indexAction()
    {
        $datagrid = $this->getDataGrid();

        $this->getXajaxManager()->registerFunctions([
            'getAvailabilityForAjax' => [$datagrid, 'getData'],
            'doDeleteAvailability'   => [$datagrid, 'delete']
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

            $this->getRepository()->save($form->getSubmitValuesFlat());

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

            $this->getRepository()->save($form->getSubmitValuesFlat(), $id);

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
        return $this->get('availability.datagrid');
    }

    /**
     * Get Repository
     */
    protected function getRepository()
    {
        return $this->get('availability.repository');
    }

    /**
     * Get Form
     */
    protected function getForm()
    {
        return $this->get('availability.form');
    }

    /**
     * Get default route
     *
     * @return string
     */
    protected function getDefaultRoute()
    {
        return 'admin.availability.index';
    }
}
