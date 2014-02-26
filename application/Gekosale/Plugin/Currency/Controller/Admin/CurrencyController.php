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
namespace Gekosale\Plugin\Currency\Controller\Admin;

use Gekosale\Core\Controller\AdminController;

/**
 * Class CurrencyController
 *
 * @package Gekosale\Plugin\Currency\Controller\Admin
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CurrencyController extends AdminController
{
    public function index()
    {
        $datagrid = $this->get('currency.datagrid');

        $this->getXajax()->registerFunction([
            'getCurrencyForAjax',
            $datagrid,
            'getData'
        ]);

        $this->getXajax()->registerFunction([
            'doDeleteCurrency',
            $datagrid,
            'doDeleteCurrency'
        ]);

        $datagrid->init();

        return Array(
            'datagrid_filter' => Array()
        );
    }

    public function add()
    {
        $form = $this->getForm()->init();

        if ($form->Validate()) {

            $this->getRepository()->save($form->getSubmitValues());

            return $this->redirect($this->generateUrl('admin.vat.index'));
        }

        return Array(
            'form' => $form
        );
    }

    public function edit($id)
    {
        $repository   = $this->get('currency.repository');
        $populateData = $repository->getPopulateData($id);
        $form         = $this->get('currency.form')->init($populateData);

        if ($form->Validate()) {

            $repository->save($form->getSubmitValues(), $id);

            return $this->redirect($this->generateUrl('admin.vat.index'));
        }

        return Array(
            'form' => $form
        );
    }

    /**
     * Get currency repository
     *
     * @return \Gekosale\Core\Repository
     */
    protected function getRepository()
    {
        return $this->get('currency.repository');
    }

    /**
     * Get currency form
     *
     * @return \Gekosale\Core\Form
     */
    protected function getForm()
    {
        return $this->get('currency.form');
    }
}
