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
 */
namespace Gekosale\Plugin;
use FormEngine;

class PlatformaRatalnaModel extends Component\Model
{

    protected $_name = 'PlatformaRatalna';

    public function getPaymentMethod ($event, $request)
    {
        $Data[$this->getName()] = $this->_name;
        $event->setReturnValues($Data);
    }

    public function getPaymentMethodConfigurationForm ($event, $request)
    {
        if ($request['data']['paymentmethodmodel'] != $this->getName()){
            return false;
        }
        
        $platformaratalna = &$request['form']->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'platformaratalna_data',
            'label' => 'Konfiguracja'
        )));
        
        $platformaratalna->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'idpartnera',
            'label' => 'Id partnera',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać Id partnera.')
            )
        )));
        
        $platformaratalna->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'notifyorderstatusid',
            'label' => 'Status zamówienia dla informacji zwrotnych',
            'options' => FormEngine\Option::Make(App::getModel('orderstatus')->getOrderStatusToSelect())
        )));
        
        $settings = $this->registry->core->loadModuleSettings('platformaratalna', Helper::getViewId());
        
        if (! empty($settings)){
            $populate = Array(
                'platformaratalna_data' => Array(
                    'idpartnera' => $settings['idpartnera'],
                    'notifyorderstatusid' => $settings['notifyorderstatusid']
                )
            );
            
            $event->setReturnValues($populate);
        }
    }

    public function saveSettings ($request)
    {
        if ($request['model'] != $this->getName()){
            return false;
        }
        
        $Settings = Array(
            'idpartnera' => $request['data']['idpartnera'],
            'notifyorderstatusid' => $request['data']['notifyorderstatusid']
        );
        
        $this->registry->core->saveModuleSettings('platformaratalna', $Settings, Helper::getViewId());
    }

    protected function formatPrice ($price)
    {
        return number_format($price, 2, '.', '');
    }

    public function getPaymentData ($order)
    {
        $settings = $this->registry->core->loadModuleSettings('platformaratalna', Helper::getViewId());
        
        $Data = Array();
        
        if (isset($order['orderData']['priceWithDispatchMethodPromo'])){
            $kwota = $this->formatPrice($order['orderData']['priceWithDispatchMethodPromo']);
        }
        else{
            $kwota = $this->formatPrice($order['orderData']['priceWithDispatchMethod']);
        }
        
        $sessionId = base64_encode(session_id() . '-' . $order['orderId']);
        
        if ($settings){
            $Data = Array(
                'idpartnera' => $settings['idpartnera'],
                'kwota' => $kwota,
                'info' => $sessionId
            );
        }
        return $Data;
    }

    public function cancelPayment ()
    {
    }

    public function confirmPayment ()
    {
    }

    public function reportPayment ()
    {
    }
}