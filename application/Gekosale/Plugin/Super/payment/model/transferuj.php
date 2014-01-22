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
 * $Revision: 528 $
 * $Author: gekosale $
 * $Date: 2011-09-12 08:54:55 +0200 (Pn, 12 wrz 2011) $
 * $Id: platnosci.php 528 2011-09-12 06:54:55Z gekosale $ 
 */
namespace Gekosale\Plugin;
use FormEngine;

class TransferujModel extends Component\Model
{

    protected $_name = 'Transferuj.pl';

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
        
        $transferuj = &$request['form']->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'transferuj_data',
            'label' => 'Konfiguracja'
        )));
        
        $transferuj->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'idsprzedawcy',
            'label' => 'Id sprzedawcy',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać Id sprzedawcy.')
            )
        ))); 
        
        $transferuj->AddChild(new FormEngine\Elements\Password(Array(
            'name' => 'kodsprzedawcy',
            'label' => 'Kod pomocniczy sprzedawcy',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać Kod pomocniczy sprzedawcy.')
            )
        )));
        
        $transferuj->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'positiveorderstatusid',
            'label' => 'Status zamówienia dla płatności zakończonej',
            'options' => FormEngine\Option::Make(App::getModel('orderstatus')->getOrderStatusToSelect())
        )));
        
        $transferuj->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'negativeorderstatusid',
            'label' => 'Status zamówienia dla płatności anulowanej',
            'options' => FormEngine\Option::Make(App::getModel('orderstatus')->getOrderStatusToSelect())
        )));
        
        $settings = $this->registry->core->loadModuleSettings('transferuj', Helper::getViewId());
        
        if (! empty($settings)){
            $populate = Array(
                'transferuj_data' => Array(
                    'idsprzedawcy' => $settings['idsprzedawcy'],
                    'kodsprzedawcy' => $settings['kodsprzedawcy'],
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
            'kodsprzedawcy' => $request['data']['kodsprzedawcy'],
            'positiveorderstatusid' => $request['data']['positiveorderstatusid'],
            'negativeorderstatusid' => $request['data']['negativeorderstatusid']
        );
        
        $this->registry->core->saveModuleSettings('transferuj', $Settings, Helper::getViewId());
    }

    public function getPaymentData ($order)
    {
        $settings = $this->registry->core->loadModuleSettings('transferuj', Helper::getViewId());
        
        if (isset($order['orderData']['priceWithDispatchMethodPromo'])){
            $kwota = $order['orderData']['priceWithDispatchMethodPromo'];
        }
        else{
            $kwota = $order['orderData']['priceWithDispatchMethod'];
        }
        
        if ($settings){
            $Data = array(
                'idsprzedawcy' => $settings['idsprzedawcy'],
                'kodsprzedawcy' => $settings['kodsprzedawcy'],
                'crc' => base64_encode(session_id() . '-' . $order['orderId']),
                'amount' => $kwota,
                'md5sum' => md5($settings['idsprzedawcy'] . $kwota . base64_encode(session_id() . '-' . $order['orderId']) . $settings['kodsprzedawcy'])
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
        if ($_SERVER['REMOTE_ADDR'] == '195.149.229.109' && ! empty($_POST)){
            
            $id_sprzedawcy = $_POST['id'];
            $status_transakcji = $_POST['tr_status'];
            $id_transakcji = $_POST['tr_id'];
            $kwota_transakcji = $_POST['tr_amount'];
            $kwota_zaplacona = $_POST['tr_paid'];
            $blad = $_POST['tr_error'];
            $data_transakcji = $_POST['tr_date'];
            $opis_transackji = $_POST['tr_desc'];
            $ciag_pomocniczy = $_POST['tr_crc'];
            $email_klienta = $_POST['tr_email'];
            $suma_kontrolna = $_POST['md5sum'];
            
            $sql = 'SELECT
						viewid,
						idorder
					FROM `order`
					WHERE sessionid = :crc';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('crc', base64_decode($ciag_pomocniczy));
            $stmt->execute();
            $rs = $stmt->fetch();
            
            if ($rs){
                $settings = $this->registry->core->loadModuleSettings('transferuj', $rs['viewid']);
                if ($status_transakcji == 'TRUE' && $blad == 'none'){
                    $status = $settings['positiveorderstatusid'];
                    $comment = 'Płatność zakończona sukcesem';
                }
                else{
                    $status = $settings['negativeorderstatusid'];
                    $comment = 'Płatność zakończona niepowodzeniem';
                }
                
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
            
            echo 'TRUE';
        }
    }
}