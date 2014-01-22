<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 566 $
 * $Author: gekosale $
 * $Date: 2011-10-19 10:34:01 +0200 (Śr, 19 paź 2011) $
 * $Id: invoice.php 566 2011-10-19 08:34:01Z gekosale $
 */
namespace Gekosale\Plugin;
use FormEngine;

class ShipmentController extends Component\Controller\Admin
{

    public function __construct ($registry)
    {
        parent::__construct($registry);
        $this->modelShipment = App::getModel('shipment/' . $this->id);
        $this->formModel = App::getFormModel('shipment/' . $this->id);
    }

    public function index ()
    {
        $this->registry->xajax->registerFunction(array(
            'LoadAllShipments',
            $this->model,
            'getShipmentForAjax'
        ));
        
        $this->renderLayout(array(
            'datagrid_filter' => $this->model->getDatagridFilterData(),
            'shipmentTitle' => $this->modelShipment->title,
            'isShipmentSelected' => strlen($this->id)
        ));
    }

    public function confirm ()
    {
        $ids = json_decode(base64_decode($this->registry->core->getParam()));
        $this->model->exportShipment($ids);
    }

    public function view ()
    {
        $pdfContent = $this->model->getPdfContentByGuid($this->id);
        header('Content-Type: application/pdf');
        header('Content-Description: File Transfer');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($pdfContent));
        header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        echo $pdfContent;
        die();
    }

    public function add ()
    {
        $orderData = App::getModel('order')->getOrderById((int) $this->registry->core->getParam(1));
        
        $settings = $this->registry->core->loadModuleSettings($this->modelShipment->module, $orderData['viewid']);
        
        foreach ($settings as $key => $value){
            if (! strlen($value)){
                unset($settings[$key]);
            }
        }
        if (empty($settings)){
            App::getContainer()->get('session')->setVolatileMessage('Musisz skonfigurować moduł ' . $this->modelShipment->title);
            App::redirect(__ADMINPANE__ . '/view/edit/' . $orderData['viewid']);
        }
        
        $populateData = $this->modelShipment->getPopulateData();
        
        $this->formModel->setPopulateData($populateData);
        
        $form = $this->formModel->initForm();
        
        if ($form->Validate(FormEngine\FE::SubmittedData())){
            
            $formData = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
            
            $shipmentId = $this->modelShipment->addShipment($formData, $orderData);
            
            App::getContainer()->get('session')->setVolatileMessage("Dodano przesyłkę {$shipmentId} do zamówienia {$this->registry->core->getParam(1)}.");
            App::redirect(__ADMINPANE__ . '/shipment/index/' . $this->id);
        }
        
        $this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
        $this->registry->xajax->processRequest();
        $this->registry->template->assign('form', $form->Render());
        $this->registry->template->display($this->loadTemplate('add.tpl'));
    }
}