<?php
/*
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2008-2014 Gekosale sp. z o.o..
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * @author Gekosale <hello@gekosale.com>
 */

namespace Gekosale\Component\Configuration\Controller\Admin;

use Gekosale\Core\Component\Controller\AdminController;

/**
 * @BaseModel("Gekosale\Component\Configuration\Model\VatModel")
 * @BaseForm("Gekosale\Component\Configuration\Form\VatForm")
 */

class VatController extends AdminController
{

    public function index()
    {
        $this->registry->xajax->registerFunction(
            array(
                 'doDeleteVAT',
                 $this->model,
                 'doAJAXDeleteVAT'
            )
        );

        $this->registry->xajax->registerFunction(
            array(
                 'LoadAllVAT',
                 $this->model,
                 'getVATForAjax'
            )
        );

        $this->registry->xajax->registerFunction(
            array(
                 'GetValueSuggestions',
                 $this->model,
                 'getValueForAjax'
            )
        );

        $this->renderLayout();
    }

    public function add()
    {
        if ($this->form->Validate()) {

            $this->model->save($this->form->getSubmitValues());

            return $this->redirect($this->generateUrl('admin.vat'));
        }

        return Array(
            'form' => $this->form
        );
    }

    public function edit($id)
    {
        $form = $this->getForm('configuration.vat');

        $form->Populate(
            $this->getModel('configuration.vat')->getPopulateData($id)
        );

        if ($form->Validate()) {

            $this->getModel('configuration.vat')->save(
                $form->getSubmitValues(),
                $id
            );

            return $this->redirect(
                $this->generateUrl('admin.vat')
            );
        }

        return Array(
            'form' => $form
        );
    }
}