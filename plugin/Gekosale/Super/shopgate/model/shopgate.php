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
 * $Revision: 612 $
 * $Author: gekosale $
 * $Date: 2011-11-28 21:02:10 +0100 (Pn, 28 lis 2011) $
 * $Id: invoice.php 612 2011-11-28 20:02:10Z gekosale $
 */
namespace Gekosale;
use FormEngine;

class ShopgateModel extends Component\Model
{

    public function addFields ($event, $request)
    {
        return false;
        
        $shopgate = &$request['form']->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'shopgate_data',
            'label' => 'Integracja z Shopgate'
        )));
        
        $shopgate->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'shopgateshopnumber',
            'label' => 'Numer sklepu'
        )));
        
        $shopgate->AddChild(new FormEngine\Elements\Password(Array(
            'name' => 'shopgatecustomernumber',
            'label' => 'Numer klienta'
        )));
        
        $shopgate->AddChild(new FormEngine\Elements\Password(Array(
            'name' => 'shopgateapikey',
            'label' => 'Klucz API'
        )));
        
        $settings = $this->registry->core->loadModuleSettings('shopgate', (int) $request['id']);
        
        if (! empty($settings)){
            $populate = Array(
                'shopgate_data' => Array(
                    'shopgateshopnumber' => $settings['shopgateshopnumber'],
                    'shopgatecustomernumber' => $settings['shopgatecustomernumber'],
                    'shopgateapikey' => $settings['shopgateapikey']
                )
            );
            
            $event->setReturnValues($populate);
        }
    }

    public function saveSettings ($request)
    {
        return false;
        
        $Settings = Array(
            'shopgateshopnumber' => $request['data']['shopgateshopnumber'],
            'shopgatecustomernumber' => $request['data']['shopgatecustomernumber'],
            'shopgateapikey' => $request['data']['shopgateapikey']
        );
        
        $this->registry->core->saveModuleSettings('shopgate', $Settings, $request['id']);
    }
}