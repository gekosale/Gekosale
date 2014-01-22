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
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: paypal.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Plugin;
use FormEngine;

class PaypalModel extends Component\Model
{

    protected $_name = 'PayPal';

    public function __construct ($registry)
    {
        parent::__construct($registry);
        
        $this->business = '';
        $this->sandbox = 1;
        
        $settings = $this->registry->core->loadModuleSettings('paypal', Helper::getViewId());
        if (! empty($settings)){
            $this->business = $settings['business'];
            $this->apiusername = $settings['apiusername'];
            $this->apipassword = $settings['apipassword'];
            $this->apisignature = $settings['apisignature'];
            $this->sandbox = $settings['sandbox'];
        }
        
        $this->gatewayurl = "https://www.paypal.com/cgi-bin/webscr";
        if ($this->sandbox === 1){
            $this->gatewayurl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        }
        $this->returnurl = App::getURLAdress() . Seo::getSeo('payment') . '/confirm';
        $this->cancelurl = App::getURLAdress() . Seo::getSeo('payment') . '/cancel';
        $this->notifyurl = App::getURLAdress() . 'paypalreport';
        $this->ipnLogFile = ROOTPATH . 'logs/paypal.ipn_results.log';
        $this->lastError = '';
        $this->ipnData = Array();
        $this->ipnResponse = '';
    }

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
        
        $paypal = &$request['form']->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'paypal_data',
            'label' => 'Konfiguracja'
        )));
        
        $paypal->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'business',
            'label' => 'Adres email',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać adres email.')
            )
        )));
        
        $paypal->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'apiusername',
            'label' => 'Nazwa użytkownika API',
            'comment' => 'Wprowadź nazwę użytkownika API',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać nazwę użytkownika API.')
            )
        )));
        
        $paypal->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'apipassword',
            'label' => 'Hasło użytkownika API',
            'comment' => 'Wprowadź hasło użytkownika API',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać hasło użytkownika API.')
            )
        )));
        
        $paypal->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'apisignature',
            'label' => 'Sygnatura API',
            'comment' => 'Wprowadź sygnaturę API',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać sygnaturę API.')
            )
        )));
        
        $paypal->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'sandbox',
            'label' => 'Sandbox',
            'options' => array(
                new FormEngine\Option(0, 'Nie (korzystaj z wersji Live)'),
                new FormEngine\Option(1, 'Tak (korzystaj z Sandbox)')
            )
        )));
        
        $paypal->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'positiveorderstatusid',
            'label' => 'Status zamówienia dla płatności zakończonej',
            'options' => FormEngine\Option::Make(App::getModel('orderstatus')->getOrderStatusToSelect()),
            'help' => 'Wybierz status zamówienia po zaakceptowaniu płatności'
        )));
        
        $paypal->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'negativeorderstatusid',
            'label' => 'Status zamówienia dla płatności anulowanej',
            'options' => FormEngine\Option::Make(App::getModel('orderstatus')->getOrderStatusToSelect()),
            'comment' => 'Wybierz status zamówienia po anulowaniu płatności'
        )));
        
        $settings = $this->registry->core->loadModuleSettings('paypal', Helper::getViewId());
        
        if (! empty($settings)){
            $populate = Array(
                'paypal_data' => Array(
                    'business' => $settings['business'],
                    'apipassword' => $settings['apipassword'],
                    'apiusername' => $settings['apiusername'],
                    'apisignature' => $settings['apisignature'],
                    'sandbox' => $settings['sandbox'],
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
            'business' => $request['data']['business'],
            'apipassword' => $request['data']['apipassword'],
            'apiusername' => $request['data']['apiusername'],
            'apisignature' => $request['data']['apisignature'],
            'sandbox' => $request['data']['sandbox'],
            'positiveorderstatusid' => $request['data']['positiveorderstatusid'],
            'negativeorderstatusid' => $request['data']['negativeorderstatusid']
        );
        
        $this->registry->core->saveModuleSettings('paypal', $Settings, Helper::getViewId());
    }

    public function validateIpn ($Data)
    {
        $urlParsed = parse_url($this->gatewayurl);
        $postString = '';
        
        foreach ($Data as $field => $value){
            $this->ipnData["$field"] = $value;
            $postString .= $field . '=' . urlencode(stripslashes($value)) . '&';
        }
        
        $postString .= "cmd=_notify-validate";
        
        // setting the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->gatewayurl);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        
        // turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        $this->ipnResponse = curl_exec($ch);
        
        if (@eregi("VERIFIED", $this->ipnResponse)){
            $this->logResults(true);
            return true;
        }
        else{
            $this->logResults(false);
            return false;
        }
    }

    public function logResults ($success)
    {
        $text = '[' . date('m/d/Y g:i A') . '] - ';
        $text .= ($success) ? "SUCCESS!\n" : 'FAIL: ' . $this->lastError . "\n";
        $text .= "IPN POST Vars from gateway:\n";
        foreach ($this->ipnData as $key => $value){
            $text .= "$key=$value, ";
        }
        $text .= "\nIPN Response from gateway Server:\n " . $this->ipnResponse;
        $fp = fopen($this->ipnLogFile, 'a');
        fwrite($fp, $text . "\n\n");
        fclose($fp);
    }

    public function cancelPayment ()
    {
    }

    public function confirmPayment ()
    {
    }

    public function reportPayment ()
    {
        $Data = $_POST;
        
        if ($this->validateIpn($Data)){
            $sql = 'SELECT
						*
					FROM `order`
					WHERE sessionid = :crc';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('crc', base64_decode($Data['custom']));
            $stmt->execute();
            $rs = $stmt->fetch();
            $settings = $this->registry->core->loadModuleSettings('paypal', $rs['viewid']);
            if ($rs){
                if ($this->ipnData['payment_status'] == 'Completed'){
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
        }
    }

    public function getPaymentData ()
    {
        $clientorder = App::getContainer()->get('session')->getActivePaymentData();
        
        if (isset($clientorder['orderData']['priceWithDispatchMethodPromo'])){
            $kwota = $clientorder['orderData']['priceWithDispatchMethodPromo'];
        }
        else{
            $kwota = $clientorder['orderData']['priceWithDispatchMethod'];
        }
        
        $Data = Array();
        $Data['rm'] = 2;
        $Data['cmd'] = '_xclick';
        $Data['business'] = $this->business;
        $Data['currency_code'] = App::getContainer()->get('session')->getActiveCurrencySymbol();
        $Data['gateway'] = $this->gatewayurl;
        $Data['return'] = $this->returnurl;
        $Data['cancel_return'] = $this->cancelurl;
        $Data['notify_url'] = $this->notifyurl;
        $Data['item_name'] = $this->trans('TXT_ORDERS_NR') . ' ' . $clientorder['orderId'];
        $Data['amount'] = round($kwota, 2);
        $Data['item_number'] = $clientorder['orderId'];
        $signature = base64_encode(session_id() . '-' . $clientorder['orderId']);
        $Data['session_id'] = $signature;
        return $Data;
    }
}