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
namespace Gekosale;
use FormEngine;

class PlatnosciModel extends Component\Model
{

    protected $_name = 'PayU';

    protected $_statusMap = Array(
        1 => 'Platnosci.pl [nowa]',
        4 => 'Platnosci.pl [rozpoczeta]',
        5 => 'Platnosci.pl [oczekuje na odbior]',
        2 => 'Platnosci.pl [anulowana]',
        3 => 'Platnosci.pl [odrzucona]',
        6 => 'Platnosci.pl [autoryzacja odmowna]',
        7 => 'Platnosci.pl [srodki odrzucone]',
        99 => 'Platnosci.pl [zakonczona]'
    );

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
        
        $payu = &$request['form']->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'payu_data',
            'label' => 'Konfiguracja'
        )));
        
        $payu->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p>Wprowadź dane punktu płatności. Adresy konieczne do ustawienia przy jego tworzeniu to:</p>
					<ul>
					<li>Adres powrotu - błąd: <strong>' . $this->registry->router->generate('frontend.payment', true, Array(
                'action' => 'cancel',
                'param' => 'platnosci'
            )) . '</strong></li>
					<li>Adres powrotu - poprawnie: <strong>' . $this->registry->router->generate('frontend.payment', true, Array(
                'action' => 'confirm',
                'param' => 'platnosci'
            )) . '</strong></li>
					<li>Adres raportów: <strong>' . $this->registry->router->generate('frontend.payment', true, Array(
                'action' => 'report',
                'param' => 'platnosci'
            )) . '</strong></li>
		
		</ul>'
        )));
        
        $payu->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'idpos',
            'label' => 'Id punktu płatności (pos_id)',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać Id punktu płatności.')
            )
        )));
        
        $payu->AddChild(new FormEngine\Elements\Password(Array(
            'name' => 'firstmd5',
            'label' => 'Klucz (MD5)',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać Klucz (MD5).')
            )
        )));
        
        $payu->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'secondmd5',
            'label' => 'Drugi klucz (MD5)',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać Drugi klucz (MD5).')
            )
        )));
        
        $payu->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'authkey',
            'label' => 'Klucz autoryzacji płatności (pos_auth_key)',
            'rules' => Array(
                new FormEngine\Rules\Required('Musisz podać Klucz autoryzacji płatności.')
            )
        )));
        
        $statuses = App::getModel('orderstatus')->getOrderStatusToSelect();
        
        $payu->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p>Ustaw powiązania statusów PayU ze sklepem</p>'
        )));
        
        foreach ($this->_statusMap as $id => $name){
            $payu->AddChild(new FormEngine\Elements\Select(Array(
                'name' => 'payu_status_' . $id,
                'label' => $name,
                'options' => FormEngine\Option::Make($statuses)
            )));
        }
        
        $settings = $this->registry->core->loadModuleSettings('payu', Helper::getViewId());
        
        if (! empty($settings)){
            $populate = Array(
                'payu_data' => Array(
                    'idpos' => $settings['idpos'],
                    'firstmd5' => $settings['firstmd5'],
                    'secondmd5' => $settings['secondmd5'],
                    'authkey' => $settings['authkey']
                )
            );
            
            foreach ($this->_statusMap as $id => $name){
                if (isset($settings['payu_status_' . $id])){
                    $populate['payu_data']['payu_status_' . $id] = $settings['payu_status_' . $id];
                }
            }
            
            $event->setReturnValues($populate);
        }
    }

    public function saveSettings ($request)
    {
        if ($request['model'] != $this->getName()){
            return false;
        }
        
        $Settings = Array(
            'idpos' => $request['data']['idpos'],
            'firstmd5' => $request['data']['firstmd5'],
            'secondmd5' => $request['data']['secondmd5'],
            'authkey' => $request['data']['authkey']
        );
        
        foreach ($this->_statusMap as $id => $name){
            $Settings['payu_status_' . $id] = $request['data']['payu_status_' . $id];
        }
        
        $this->registry->core->saveModuleSettings('payu', $Settings, Helper::getViewId());
    }

    public function getPaymentData ($order)
    {
        $settings = $this->registry->core->loadModuleSettings('payu', Helper::getViewId());
        
        if (isset($order['orderData']['priceWithDispatchMethodPromo'])){
            $amount = $order['orderData']['priceWithDispatchMethodPromo'] * 100;
        }
        else{
            $amount = $order['orderData']['priceWithDispatchMethod'] * 100;
        }
        
        $Data = Array();
        if ($settings){
            $Data['language'] = 'PL';
            $Data['session_id'] = session_id() . '-' . $order['orderId'];
            $Data['order_id'] = $order['orderId'];
            $Data['js'] = 1;
            $Data['pos_id'] = $settings['idpos'];
            $Data['pos_auth_key'] = $settings['authkey'];
            $Data['amount'] = $amount;
            $Data['desc'] = 'Zamowienie ' . $order['orderId'] . ' - ' . $order['orderData']['clientaddress']['firstname'] . ' ' . $order['orderData']['clientaddress']['surname'];
            $Data['first_name'] = $order['orderData']['clientaddress']['firstname'];
            $Data['last_name'] = $order['orderData']['clientaddress']['surname'];
            $Data['street'] = $order['orderData']['clientaddress']['street'];
            $Data['street_hn'] = $order['orderData']['clientaddress']['streetno'];
            $Data['city'] = $order['orderData']['clientaddress']['placename'];
            $Data['post_code'] = $order['orderData']['clientaddress']['postcode'];
            $Data['country'] = 'Poland';
            $Data['phone'] = $order['orderData']['contactData']['phone'];
            $Data['email'] = $order['orderData']['contactData']['email'];
            $Data['client_ip'] = $_SERVER["REMOTE_ADDR"];
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
        $Data = $_POST;
        
        $settings = $this->registry->core->loadModuleSettings('payu', Helper::getViewId());
        
        $sig = md5($Data['pos_id'] . $Data['session_id'] . $Data['ts'] . $settings['secondmd5']);
        if ($Data['sig'] != $sig){
            die('ERROR: WRONG SIGNATURE');
        }
        
        $ts = time();
        $sig = md5($settings['idpos'] . $Data['session_id'] . $ts . $settings['firstmd5']);
        
        $server = 'https://www.platnosci.pl';
        $server_script = '/paygw/UTF/Payment/get/';
        $parameters = "?pos_id=" . $settings['idpos'] . "&session_id=" . $Data['session_id'] . "&ts=" . $ts . "&sig=" . $sig;
        
        $url = $server . $server_script . $parameters;
        
        $url = str_replace("&amp;", "&", urldecode(trim($url)));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        $content = curl_exec($ch);
        $response = curl_getinfo($ch);
        curl_close($ch);
        
        $str = simplexml_load_string($content);
        
        foreach ($str->trans as $trans){
            if ($trans->status == 99 || ($trans->status > 0 && $trans->status <= 7)){
                $idstatus = (int) $trans->status;
                $idorder = (int) $trans->order_id;
                
                $orderStatusId = $settings['payu_status_' . $idstatus];

                $sql = 'SELECT
						  *
					    FROM `order`
					    WHERE idorder = :id';
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('id', $idorder);
                $stmt->execute();
                $rs = $stmt->fetch();

                if ($rs){
                    $sql = 'INSERT INTO orderhistory(content, orderstatusid, orderid, inform)
                            VALUES (:content, :orderstatusid, :orderid, :inform)';
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('content', $this->_statusMap[$idstatus]);
                    $stmt->bindValue('orderstatusid', $orderStatusId);
                    $stmt->bindValue('orderid', $idorder);
                    $stmt->bindValue('inform', 0);
                    $stmt->execute();

                    $sql = 'UPDATE `order` SET orderstatusid = :orderstatusid WHERE idorder = :orderid';
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('orderstatusid', $orderStatusId);
                    $stmt->bindValue('orderid', $idorder);
                    $stmt->execute();
                }

                echo 'OK';
            }
            else{
                echo "ERROR";
            }
        }
    }
}