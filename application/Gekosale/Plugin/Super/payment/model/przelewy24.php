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

class Przelewy24Model extends Component\Model
{

    protected $_name = 'Przelewy24';

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
        
        $przelewy24 = &$request['form']->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'przelewy24_data',
            'label' => 'Konfiguracja'
        )));
        
        $przelewy24->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'idsprzedawcy',
            'label' => 'Id sprzedawcy',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać Id sprzedawcy.')
            )
        )));
        
        $przelewy24->AddChild(new FormEngine\Elements\Password(Array(
            'name' => 'crc',
            'label' => 'Klucz do CRC',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać Klucz do CRC.')
            )
        )));
        
        $przelewy24->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'positiveorderstatusid',
            'label' => 'Status zamówienia dla płatności zakończonej',
            'options' => FormEngine\Option::Make(App::getModel('orderstatus')->getOrderStatusToSelect())
        )));
        
        $przelewy24->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'negativeorderstatusid',
            'label' => 'Status zamówienia dla płatności anulowanej',
            'options' => FormEngine\Option::Make(App::getModel('orderstatus')->getOrderStatusToSelect())
        )));
        
        $settings = $this->registry->core->loadModuleSettings('przelewy24', Helper::getViewId());
        
        if (! empty($settings)){
            $populate = Array(
                'przelewy24_data' => Array(
                    'idsprzedawcy' => $settings['idsprzedawcy'],
                    'crc' => $settings['crc'],
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
            'crc' => $request['data']['crc'],
            'positiveorderstatusid' => $request['data']['positiveorderstatusid'],
            'negativeorderstatusid' => $request['data']['negativeorderstatusid']
        );
        
        $this->registry->core->saveModuleSettings('przelewy24', $Settings, Helper::getViewId());
    }

    protected function formatPrice ($price)
    {
        return number_format($price * 100, 0, '', '');
    }

    public function getPaymentData ($order)
    {
        $settings = $this->registry->core->loadModuleSettings('przelewy24', Helper::getViewId());
        
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
                'idsprzedawcy' => $settings['idsprzedawcy'],
                'kwota' => $kwota,
                'sessionid' => $sessionId,
                'crc' => md5($sessionId . '|' . $settings['idsprzedawcy'] . '|' . ($kwota) . '|' . $settings['crc'])
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
        if (isset($_POST['p24_session_id']) && $_POST['p24_session_id'] != ''){
            
            $sessionid = base64_decode($_POST['p24_session_id']);
            
            $sql = 'SELECT 
						*
					FROM `order`
					WHERE sessionid = :crc';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('crc', base64_decode($_POST['p24_session_id']));
            $stmt->execute();
            $rs = $stmt->fetch();
            
            if ($rs){
                
                $settings = $this->registry->core->loadModuleSettings('przelewy24', $rs['viewid']);
                
                $p24_session_id = $_POST["p24_session_id"];
                $p24_order_id = $_POST["p24_order_id"];
                $p24_kwota = number_format($rs['globalprice'] * 100, 0, '', '');
                
                $P = array();
                $RET = array();
                $url = "https://secure.przelewy24.pl/transakcja.php";
                $P[] = "p24_id_sprzedawcy=" . $settings['idsprzedawcy'];
                $P[] = "p24_session_id=" . $p24_session_id;
                $P[] = "p24_order_id=" . $p24_order_id;
                $P[] = "p24_kwota=" . $p24_kwota;
                $user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, join("&", $P));
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                $result = curl_exec($ch);
                curl_close($ch);
                $T = explode(chr(13) . chr(10), $result);
                $res = false;
                foreach ($T as $line){
                    $line = ereg_replace("[\n\r]", "", $line);
                    if ($line != "RESULT" and ! $res)
                        continue;
                    if ($res)
                        $RET[] = $line;
                    else
                        $res = true;
                }
                
                if ($RET[0] == 'TRUE'){
                    $status = $settings['positiveorderstatusid'];
                    $comment = 'Płatność zakończona sukcesem';
                    $url = $this->registry->router->generate('frontend.payment', true, Array(
                        'action' => 'confirm',
                        'param' => 'przelewy24'
                    ));
                }
                else{
                    $status = $settings['negativeorderstatusid'];
                    $comment = 'Płatność zakończona niepowodzeniem';
                    $url = $this->registry->router->generate('frontend.payment', true, Array(
                        'action' => 'cancel',
                        'param' => 'przelewy24'
                    ));
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
                
                if (App::getContainer()->get('session')->getActivePaymentData() != NULL){
                    App::redirectUrl($url);
                }
            }
        }
    }
}