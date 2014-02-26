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
    public function index()
    {
        $this->getDataGrid()->init();

        return Array(
            'datagrid_filter' => $this->getDataGrid()->getFilterData()
        );
    }

    public function add()
    {
        $form = $this->getForm()->init();

        if ($form->Validate()) {

            $this->getRepository()->save($form->getSubmitValues());

            return $this->redirect($this->getActionRoute('index'));
        }

        return Array(
            'form' => $form
        );
    }

    public function edit($id)
    {
        $populateData = $this->getRepository()->getPopulateData($id);
        $form         = $this->getForm()->init($populateData);

        if ($form->Validate()) {

            $this->getRepository()->save($form->getSubmitValues(), $id);

            return $this->redirect($this->getActionRoute('index'));
        }

        return Array(
            'form' => $form
        );
    }

    protected function getActionRoute($action)
    {
        return $this->generateUrl(sprintf('%s.%s', $this->getPluginAlias(), $action));
    }

    abstract protected function getRepository();

    abstract protected function getDataGrid();

    abstract protected function getForm();

    abstract protected function getPluginAlias();

}