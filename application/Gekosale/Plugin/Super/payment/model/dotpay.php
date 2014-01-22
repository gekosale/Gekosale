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

class DotpayModel extends Component\Model
{

    protected $_name = 'DotPay.pl';

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
        
        $dotpay = &$request['form']->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'dotpay_data',
            'label' => 'Konfiguracja'
        )));
        
        $dotpay->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'idsprzedawcy',
            'label' => 'Id sprzedawcy',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać Id sprzedawcy.')
            )
        )));
        
        $dotpay->AddChild(new FormEngine\Elements\Password(Array(
            'name' => 'pin',
            'label' => 'PIN',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać PIN.')
            )
        )));
        
        $dotpay->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'positiveorderstatusid',
            'label' => 'Status zamówienia dla płatności zakończonej',
            'options' => FormEngine\Option::Make(App::getModel('orderstatus')->getOrderStatusToSelect())
        )));
        
        $dotpay->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'negativeorderstatusid',
            'label' => 'Status zamówienia dla płatności anulowanej',
            'options' => FormEngine\Option::Make(App::getModel('orderstatus')->getOrderStatusToSelect())
        )));
        
        $settings = $this->registry->core->loadModuleSettings('dotpay', Helper::getViewId());
        
        if (! empty($settings)){
            $populate = Array(
                'dotpay_data' => Array(
                    'idsprzedawcy' => $settings['idsprzedawcy'],
                    'pin' => $settings['pin'],
                    'positiveorderstatusid' => $settings['positiveorderstatusid'],
                    'negativeorderstatusid' => $settings['negativeorderstatusid']
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
            'idsprzedawcy' => $request['data']['idsprzedawcy'],
            'pin' => $request['data']['pin'],
            'positiveorderstatusid' => $request['data']['positiveorderstatusid'],
            'negativeorderstatusid' => $request['data']['negativeorderstatusid']
        );
        
        $this->registry->core->saveModuleSettings('dotpay', $Settings, Helper::getViewId());
    }

    protected function formatPrice ($price)
    {
        return number_format($price * 100, 0, '', '');
    }

    public function getPaymentData ($order)
    {
        $settings = $this->registry->core->loadModuleSettings('dotpay', Helper::getViewId());
        
        $Data = Array();
        
        if (isset($order['orderData']['priceWithDispatchMethodPromo'])){
            $kwota = $this->formatPrice($order['orderData']['priceWithDispatchMethodPromo']);
        }
        else{
            $kwota = $this->formatPrice($order['orderData']['priceWithDispatchMethod']);
        }
        
        if ($settings){
            $crc = base64_encode(session_id() . '-' . $order['orderId']);
            $Data = Array(
                'idsprzedawcy' => $settings['idsprzedawcy'],
                'pin' => $settings['pin'],
                'crc' => $crc,
                'md5sum' => md5($settings['idsprzedawcy'] . $kwota . $crc . $settings['pin'])
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
        if (in_array($_SERVER['REMOTE_ADDR'], Array(
            '195.150.9.37',
            '217.17.41.5'
        )) && ! empty($_POST)){
            
            $sql = 'SELECT
						*
					FROM `order`
					WHERE sessionid = :crc';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('crc', base64_decode($_POST['control']));
            $stmt->execute();
            $rs = $stmt->fetch();
            
            if ($rs){
                
                $settings = $this->registry->core->loadModuleSettings('dotpay', $rs['viewid']);
                
                $id_sprzedawcy = $_POST['id'];
                
                $m5 = $settings['pin'] . ':' . $settings['idsprzedawcy'] . ':' . $_POST['control'] . ':' . $_POST['t_id'] . ':' . $_POST['amount'] . ':' . $_POST['email'] . ':' . $_POST['service'] . ':' . $_POST['code'] . ':' . $_POST['username'] . ':' . $_POST['password'] . ':' . $_POST['t_status'];
                $status_transakcji = $_POST['t_status'];
                $id_transakcji = $_POST['t_id'];
                $kwota_transakcji = $_POST['amount'];
                $email_klienta = $_POST['email'];
                $suma_kontrolna = $_POST['md5'];
                
                $status = 0;
                
                if ($_POST['t_status'] == 2){
                    $status = $settings['positiveorderstatusid'];
                    $comment = 'Płatność zakończona sukcesem';
                }
                if ($_POST['t_status'] == 3){
                    $status = $settings['negativeorderstatusid'];
                    $comment = 'Płatność zakończona niepowodzeniem';
                }
                
                if ($status > 0){
                    $sql = "UPDATE `order` SET orderstatusid = :status WHERE idorder = :idorder";
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('status', $status);
                    $stmt->bindValue('idorder', $rs['idorder']);
                    $stmt->execute();
                    
                    $sql = 'INSERT INTO orderhistory SET
								content = :content,
								orderstatusid = :status,
								orderid = :idorder,
								inform = 0';
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('content', $comment);
                    $stmt->bindValue('idorder', $rs['idorder']);
                    $stmt->bindValue('status', $status);
                    $stmt->execute();
                }
            }
            
            print "OK";
            exit();
        }
    }
}
