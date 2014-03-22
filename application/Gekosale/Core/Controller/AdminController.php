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
namespace Gekosale\Core\Controller;

use Gekosale\Core\Controller;

/**
 * Class AdminController
 *
 * @package Gekosale\Core\Controller
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
abstract class AdminController extends Controller
{

    /**
     * Default indexAction logic for all controllers
     *
     * @return array
     */
    public function indexAction()
    {
        $datagrid = $this->getDataGrid();

        $datagrid->configure();

        $datagrid->init();

        return [
            'datagrid' => $datagrid,
        ];
    }

    /**
     * Default addAction logic for all controllers
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAction()
    {
        $form = $this->getForm()->init();

        if ($this->getRequest()->isMethod('POST') && $form->isValid()) {

            $this->getRepository()->save($form->getSubmitValuesFlat());

            return $this->redirect($this->generateUrl($this->getDefaultRoute()));
        }

        return [
            'form' => $form
        ];
    }

    /**
     * Default editAction logic for all controllers
     *
     * @param $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction($id)
    {
        $populateData = $this->getRepository()->getPopulateData($id);
        $form         = $this->getForm()->init($populateData);

        if ($this->getRequest()->isMethod('POST') && $form->isValid()) {

            $this->getRepository()->save($form->getSubmitValuesFlat(), $id);

            return $this->redirect($this->generateUrl($this->getDefaultRoute()));
        }

        return [
            'form' => $form
        ];
    }

    /**
     * Returns repository service for controller
     *
     * @return \Gekosale\Core\Repository|object
     */
    abstract protected function getRepository();

    /**
     * Returns DataGrid service for controller
     *
     * @return \Gekosale\Core\DataGrid|object
     */
    abstract protected function getDataGrid();

    /**
     * Returns Form service for controller
     *
     * @return \Gekosale\Core\Form|object
     */
    abstract protected function getForm();
}