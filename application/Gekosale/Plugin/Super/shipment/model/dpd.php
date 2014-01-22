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
namespace Gekosale\Plugin;
use DpdApi;
use FormEngine;
use SoapClient;

include_once (ROOTPATH . 'lib' . DS . 'DpdApi' . DS . 'DpdApi.php');

class DpdModel extends Component\Model
{

    protected $settings;

    public $title = 'DPD';

    public $module = 'dpd';

    public function addFields ($event, $request)
    {
        $form = &$request['form'];
        
        $dpd = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'dpd_data',
            'label' => 'Integracja z DPD'
        )));
        
        $dpd->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'dpdlogin',
            'label' => 'Login'
        )));
        
        $dpd->AddChild(new FormEngine\Elements\Password(Array(
            'name' => 'dpdpassword',
            'label' => 'Hasło'
        )));
        
        $dpd->AddChild(new FormEngine\Elements\Password(Array(
            'name' => 'dpdmasterfid',
            'label' => 'Master FID'
        )));
        
        $dpd->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'dpdcompany',
            'label' => 'Nazwa firmy'
        )));
        
        $dpd->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'dpdname',
            'label' => 'Nadawca'
        )));
        $dpd->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'dpdstreet',
            'label' => 'Adres'
        )));
        
        $dpd->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'dpdcity',
            'label' => 'Miasto'
        )));
        
        $dpd->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'dpdpostalcode',
            'label' => 'Kod pocztowy'
        )));
        
        $dpd->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'dpdphone',
            'label' => 'Telefon kontaktowy'
        )));
        
        $dpd->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'dpdemail',
            'label' => 'Adres e-mail'
        )));
        
        $settings = $this->registry->core->loadModuleSettings('dpd', (int) $request['id']);
        
        if (! empty($settings)){
            $populate = Array(
                'dpd_data' => Array(
                    'dpdlogin' => $settings['dpdlogin'],
                    'dpdpassword' => $settings['dpdpassword'],
                    'dpdmasterfid' => $settings['dpdmasterfid'],
                    'dpdcompany' => $settings['dpdcompany'],
                    'dpdname' => $settings['dpdname'],
                    'dpdstreet' => $settings['dpdstreet'],
                    'dpdcity' => $settings['dpdcity'],
                    'dpdpostalcode' => $settings['dpdpostalcode'],
                    'dpdphone' => $settings['dpdphone'],
                    'dpdemail' => $settings['dpdemail']
                )
            );
            
            $event->setReturnValues($populate);
        }
    }

    public function saveSettings ($request)
    {
        $Settings = Array(
            'dpdlogin' => $request['data']['dpdlogin'],
            'dpdpassword' => $request['data']['dpdpassword'],
            'dpdmasterfid' => $request['data']['dpdmasterfid'],
            'dpdcompany' => $request['data']['dpdcompany'],
            'dpdname' => $request['data']['dpdname'],
            'dpdstreet' => $request['data']['dpdstreet'],
            'dpdcity' => $request['data']['dpdcity'],
            'dpdpostalcode' => $request['data']['dpdpostalcode'],
            'dpdphone' => $request['data']['dpdphone'],
            'dpdemail' => $request['data']['dpdemail']
        );
        
        $this->registry->core->saveModuleSettings('dpd', $Settings, $request['id']);
    }

    public function getPopulateData ()
    {
        $Data = Array(
            'shipment_data' => Array(
                'codamount' => 0,
                'comment' => $this->trans('TXT_SHIPMENT_ADDED_DPD')
            )
        );
        
        return $Data;
    }

    public function addShipment ($formData, $orderData)
    {
        $settings = $this->registry->core->loadModuleSettings('dpd', $orderData['viewid']);
        if ($settings['dpdlogin'] == 'test'){
            $this->Url = "https://dpdservicesdemo.dpd.com.pl/DPDPackageXmlServicesService/DPDPackageXmlServices?WSDL";
        }
        else{
            $this->Url = "https://dpdservices.dpd.com.pl/DPDPackageXmlServicesService/DPDPackageXmlServices?WSDL";
        }
        
        $sumWeight = 0;
        foreach ($orderData['products'] as $item){
            if (! empty($item['attributes'])){
                foreach ($item['attributes'] as $attr){
                    $sumWeight = $sumWeight + ($attr['weight'] * $item['quantity']);
                }
            }
            else{
                $sumWeight = $sumWeight + ($item['weight'] * $item['quantity']);
            }
        }
        
        if ($sumWeight < 0.1){
            $sumWeight = 0.1;
        }
        
        $guid = App::getModel('shipment')->getGuid();
        
        $shipFromDpd["Company"] = $settings['dpdcompany'];
        $shipFromDpd["Name"] = $settings['dpdname'];
        $shipFromDpd["Street"] = $settings['dpdstreet'];
        $shipFromDpd["City"] = $settings['dpdcity'];
        $shipFromDpd["PostalCode"] =  preg_replace('/[^0-9]/','', $settings['dpdpostalcode']);
        $shipFromDpd["CountryCode"] = "PL";
        $shipFromDpd["Phone"] = $settings['dpdphone'];
        $shipFromDpd["Email"] = $settings['dpdemail'];
        
        $shipToDpd["Company"] = $orderData['delivery_address']['companyname'];
        $shipToDpd["Name"] = $orderData['delivery_address']['firstname'];
        $shipToDpd["Surname"] = $orderData['delivery_address']['surname'];
        $shipToDpd["Street"] = $orderData['delivery_address']['street'];
        $shipToDpd["Number"] = $orderData['delivery_address']['streetno'] . ' ' . $orderData['delivery_address']['placeno'];
        $shipToDpd["City"] = $orderData['delivery_address']['city'];
        $shipToDpd["CountryCode"] = 'PL';
        $shipToDpd["PostalCode"] = preg_replace('/[^0-9]/','', $orderData['delivery_address']['postcode']);
        $phone = App::getModel('shipment')->parseNumber($orderData['delivery_address']['phone']);
        $shipToDpd["Phone"] = $phone;
        $shipToDpd["Phone2"] = '';
        $shipToDpd["Email"] = $orderData['delivery_address']['email'];
        
        $packageDetails["package_amount"] = 1;
        $packageDetails["customer_data_1"] = $this->trans('TXT_ORDER') . $orderData['order_id'];
        $packageDetails["package_content"] = '';
        $packageDetails["reference_number"] = $guid;
        $packageDetails["Ref1"] = $orderData['order_id'];
        $packageDetails["Ref2"] = '';
        $packageDetails["Ref3"] = '';
        $packageDetails["COD"] = $formData['codamount'];
        $packageDetails["DeclaredValue"] = $orderData['total'];
        $packageDetails["Weight"] = $sumWeight;
        
        $dpd = new DpdApi();
        $dpd->setLang("pl_PL");
        $dpd->setHost($this->Url);
        $dpd->setFolder(ROOTPATH . 'upload');
        $dpd->setLogin($settings['dpdlogin']);
        $dpd->setPassword($settings['dpdpassword']);
        $dpd->setMasterfid($settings['dpdmasterfid']);
        
        $dpd->setDepartment(1);
        $dpd->setConnection();
        $dpd->setShipFrom($shipFromDpd);
        $dpd->setShipTo($shipToDpd);
        $dpd->setPackageDetails($packageDetails);
        
        $response = json_decode(json_encode($dpd->registerNewPackage()), true);
        
        if(isset($response['array']['first_waybill'][0])){
        	$dispatcherNumber = (string) $response['array']['first_waybill'][0];
        } else {
            if(isset($response['message'])) {
                 $msg = $response['message'];
                
                 if(strpos($response['message'], 'w polu') !== false) {
                     $msg = explode(',', $msg);
                     $msg = $msg[0].'.';
                     
                 } else if(strpos($response['message'], 'Kod błędu') !== false) {
                     $msg = explode('.', $msg);
                     array_pop($msg);
                     $msg = implode('. ', $msg);
                     $msg .= '.';
                 }
                         
                 App::getContainer()->get('session')->setVolatileMessage($msg);
            } else {
                App::getContainer()->get('session')->setVolatileMessage("Wystąpił nieoczekiwany błąd.");
            }
            return App::redirect(__ADMINPANE__ . '/shipment');
	        // tu w przyszłości zrobić obsługę błędów
	        // Gekosale\Arr::debug($response); 
        }
        
        $label = $dpd->getLabelPDF(1, $guid);
        
        if (strlen($dispatcherNumber) > 0){
            $sql = "INSERT INTO shipments SET
						orderid = :orderid,
						guid = :guid,
						packagenumber = :packagenumber,
						label = :label,
						adddate = :adddate,
						orderdata = :orderdata,
						formdata = :formdata,
						model = :model,
						sent = 1";
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('orderid', $orderData['order_id']);
            $stmt->bindValue('guid', $guid);
            $stmt->bindValue('packagenumber', $dispatcherNumber);
            $stmt->bindValue('label', current($label));
            $stmt->bindValue('adddate', date('Y-m-d H:i:s'));
            $stmt->bindValue('orderdata', serialize($orderData));
            $stmt->bindValue('formdata', serialize($formData));
            $stmt->bindValue('model', $this->module);
            $stmt->execute();

            if((int)$formData['orderstatus'] > 0){
                $sql = 'UPDATE `order` SET orderstatusid = :status WHERE idorder = :id';
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('status', $formData['orderstatus']);
                $stmt->bindValue('id', $orderData['order_id']);
                $stmt->execute();
            }

            App::getModel('order')->addOrderHistory(Array(
                'comment' => str_replace('{PACKAGE_NUMBER}', $dispatcherNumber, $formData['comment']),
                'inform' => $formData['notifyuser'],
                'status' => $formData['orderstatus']
            ), $orderData['order_id']);

            if ($formData['notifyuser'] == 1){
                $order = App::getModel('order')->getOrderById((int) $orderData['order_id']);
                App::getModel('order')->notifyUser($order, $formData['orderstatus']);
            }
        }
        return $dispatcherNumber;
    }

    public function getProtocol($references) {
        $settings = $this->registry->core->loadModuleSettings('dpd', (int) $request['id']);
        
        $dpd = new DpdApi();
        $dpd->setLang("pl_PL");
        $dpd->setHost("https://dpdservices.dpd.com.pl/DPDPackageXmlServicesService/DPDPackageXmlServices?WSDL");
        $dpd->setFolder(ROOTPATH . 'upload');
        
        $dpd->setLogin($settings['dpdlogin']);
        $dpd->setPassword($settings['dpdpassword']);
        $dpd->setMasterfid($settings['dpdmasterfid']);
        
        $dpd->setDepartment(1);
        $dpd->setConnection();
        
        $dpdResponse = $dpd->getProtocol($references);

        
        if(array_key_exists($dpdResponse, 'message') || !is_object($dpdResponse)) {
            if(array_key_exists($dpdResponse, 'message')) {
                App::getContainer()->get('session')->setVolatileMessage($dpdResponse['message']);
            } else {
                App::getContainer()->get('session')->setVolatileMessage($dpdResponse);
            }
            return App::redirect(__ADMINPANE__ . '/shipment');
        } else {
            $pdfContent = base64_decode(current($dpdResponse));
        
            header('Content-Type: application/pdf');
            header('Content-Description: File Transfer');
            header('Content-Transfer-Encoding: binary');
            //header('Content-Length: ' . strlen($pdfContent));
            header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0');
            header('Expires: 0');
            echo $pdfContent;
            die();
        }
    }
}