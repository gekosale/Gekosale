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
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: news.php 619 2011-12-19 21:09:00Z gekosale $
 */
namespace Gekosale\Plugin;
use FormEngine;

class ElektronicznyNadawcaForm extends Component\Form
{

    protected $populateData;

    public function setPopulateData ($Data)
    {
        $this->populateData = $Data;
    }

    public function initForm ()
    {
        $form = new FormEngine\Elements\Form(Array(
            'name' => 'shipment',
            'action' => '',
            'method' => 'post'
        ));
        
        $shipmentData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'shipment_data',
            'label' => $this->trans('TXT_SHIPMENT')
        )));
        
        $shipmentData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'urzad_nadania',
            'label' => 'Urząd nadania',
            'options' => FormEngine\Option::Make($this->populateData['shipment_data']['urzad_nadania'])
        )));
        
        // 		$shipmentData->AddChild(new FormEngine\Elements\Select(Array(
        // 			'name' => 'shipmenttype',
        // 			'label' => 'Kategoria',
        // 			'options' => FormEngine\Option::Make($this->populateData['shipment_data']['shipmenttype'])
        // 		)));
        

        $shipmentData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'gabaryt',
            'label' => 'Gabaryt',
            'options' => FormEngine\Option::Make($this->populateData['shipment_data']['gabaryt'])
        )));
        
        $summary = App::getModel('order')->getOrderTotals((int) $this->registry->core->getParam(1));
        
        $shipmentData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'codamount',
            'label' => $this->trans('TXT_COD_AMOUNT'),
            'default' => '0.00'
        )));
        
        $shipmentData->AddChild(new FormEngine\Elements\Textarea(Array(
            'name' => 'comment',
            'label' => $this->trans('TXT_COMMENT'),
            'default' => $this->trans('TXT_SHIPMENT_ADDED_TO_ORDER') . $orderData['order_id']
        )));
        
        $shipmentData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'orderstatus',
            'label' => $this->trans('TXT_CHANGE_ORDER_STATUS'),
            'options' => FormEngine\Option::Make(App::getModel('orderstatus')->getOrderStatusToSelect($this->trans('TXT_STATUS_NO_CHANGE')))
        )));
        
        $shipmentData->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'notifyuser',
            'label' => $this->trans('TXT_NOTIFY_USER')
        )));
        
        $form->AddFilter(new FormEngine\Filters\Trim());
        $form->AddFilter(new FormEngine\Filters\Secure());
        
        $Data = Event::dispatch($this, 'admin.shipment.initForm', Array(
            'form' => $form,
            'id' => (int) $this->registry->core->getParam(),
            'data' => $this->populateData
        ));
        
        if (! empty($Data)){
            $form->Populate($Data);
        }
        
        $form->AddFilter(new FormEngine\Filters\Trim());
        $form->AddFilter(new FormEngine\Filters\Secure());
        
        return $form;
    }
}