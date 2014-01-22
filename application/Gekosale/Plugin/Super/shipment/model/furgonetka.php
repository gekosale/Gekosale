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
use FormEngine;

class FurgonetkaModel extends Component\Model
{

    protected $settings;

    public function __construct ($registry)
    {
        parent::__construct($registry);
        
        $this->Url = "https://dpdservices.dpd.com.pl/DPDPackageXmlServicesService/DPDPackageXmlServices?WSDL";
        $this->Login = "9587901";
        $this->Password = "LXOnJ4dGQYQeQIdEkxzA";
        $this->Masterfid = 95879;
        
        // 		$this->Url = "https://dpdservicesdemo.dpd.com.pl/DPDPackageXmlServicesService/DPDPackageXmlServices?WSDL";
        // 		$this->Login = "test";
        // 		$this->Password = "KqvsoFLT2M";
        // 		$this->Masterfid= 1495;
    }

    public function addFields ($event, $request)
    {
        return false;
        
        $furgonetka = &$request['form']->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'furgonetka_data',
            'label' => 'Integracja z Furgonetka'
        )));
        
        $furgonetka->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'furgonetkalogin',
            'label' => 'Login'
        )));
        
        $furgonetka->AddChild(new FormEngine\Elements\Password(Array(
            'name' => 'furgonetkapassword',
            'label' => 'Hasło'
        )));
        
        $settings = $this->registry->core->loadModuleSettings('furgonetka', (int) $request['id']);
        
        if (! empty($settings)){
            $populate = Array(
                'furgonetka_data' => Array(
                    'furgonetkalogin' => $settings['furgonetkalogin'],
                    'furgonetkapassword' => $settings['furgonetkapassword'],
                )
            );
            
            $event->setReturnValues($populate);
        }
    }

    public function saveSettings ($request)
    {
        return false;
        $Settings = Array(
            'furgonetkalogin' => $request['data']['furgonetkalogin'],
            'furgonetkapassword' => $request['data']['furgonetkapassword'],
        );
        
        $this->registry->core->saveModuleSettings('furgonetka', $Settings, $request['id']);
    }

    public function getPopulateData ()
    {
        $Data = Array(
            'shipment_data' => Array(
                'codamount' => 0
            )
        );
        
        return $Data;
    }

    public function addShipment ($formData, $orderData)
    {
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
        
        if ($sumWeight < 0.01){
            $sumWeight = 0.01;
        }
        
        $guid = App::getModel('shipment')->getGuid();
        
        $shipFromDpd["Company"] = "ECOSPA SUPPLY Artur Chaber";
        $shipFromDpd["Name"] = "Rita Kozak-Chaber";
        $shipFromDpd["Street"] = "ul. Wilcza 31/1a";
        $shipFromDpd["City"] = "Warszawa";
        $shipFromDpd["PostalCode"] = "00544";
        $shipFromDpd["CountryCode"] = "PL";
        $shipFromDpd["Phone"] = "48227970873";
        $shipFromDpd["Email"] = "info@ecospa.pl";
        
        $shipToDpd["Company"] = $orderData['delivery_address']['companyname'];
        $shipToDpd["Name"] = $orderData['delivery_address']['firstname'];
        $shipToDpd["Surname"] = $orderData['delivery_address']['surname'];
        $shipToDpd["Street"] = $orderData['delivery_address']['street'];
        $shipToDpd["Number"] = $orderData['delivery_address']['streetno'] . ' ' . $orderData['delivery_address']['placeno'];
        $shipToDpd["City"] = $orderData['delivery_address']['city'];
        $shipToDpd["CountryCode"] = 'PL';
        $shipToDpd["PostalCode"] = str_replace('-', '', $orderData['delivery_address']['postcode']);
        $phone = App::getModel('shipment')->parseNumber($orderData['delivery_address']['phone']);
        $shipToDpd["Phone"] = $phone;
        $shipToDpd["Phone2"] = '';
        $shipToDpd["Email"] = $orderData['delivery_address']['email'];
        
        $packageDetails["package_amount"] = 1;
        $packageDetails["customer_data_1"] = 'Zamówienie ' . $orderData['order_id'];
        $packageDetails["package_content"] = 'Surowce kosmetyczne';
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
        
        $dpd->setLogin($this->Login);
        $dpd->setPassword($this->Password);
        $dpd->setMasterfid($this->Masterfid);
        
        // $dpd->setLogin("test");
        // $dpd->setPassword("KqvsoFLT2M");
        // $dpd->setMasterfid(1495);
        

        $dpd->setDepartment(1);
        $dpd->setConnection();
        $dpd->setShipFrom($shipFromDpd);
        $dpd->setShipTo($shipToDpd);
        $dpd->setPackageDetails($packageDetails);
        
        $response = json_decode(json_encode($dpd->registerNewPackage()), true);
        
        $dispatcherNumber = (string) $response['array']['first_waybill'][0];
        
        $label = $dpd->getLabelPDF(1, $guid);
        
        if (strlen($dispatcherNumber) > 0){
            $sql = "INSERT INTO shipment SET
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
            $stmt->bindValue('model', 'dpd');
            $stmt->execute();
            
            $status = 12;
            $sql = 'UPDATE `order` SET orderstatusid = :status WHERE idorder = :id';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('status', $status);
            $stmt->bindValue('id', $orderData['order_id']);
            $stmt->execute();
            
            App::getModel('order')->addOrderHistory(Array(
                'comment' => 'Twoje zamówienie zostało wysłane kurierem DPD. Numer przesyłki: ' . $dispatcherNumber,
                'inform' => 1,
                'status' => $status
            ), $orderData['order_id']);
            
            $order = App::getModel('order')->getOrderById((int) $orderData['order_id']);
            App::getModel('order')->notifyUser($order, $status);
        }
        return $dispatcherNumber;
    }

    public function getProtocol ($references)
    {
        $dpd = new DpdApi();
        $dpd->setLang("pl_PL");
        $dpd->setHost("https://dpdservices.dpd.com.pl/DPDPackageXmlServicesService/DPDPackageXmlServices?WSDL");
        $dpd->setFolder(ROOTPATH . 'upload');
        
        $dpd->setLogin($this->Login);
        $dpd->setPassword($this->Password);
        $dpd->setMasterfid($this->Masterfid);
        
        $dpd->setDepartment(1);
        $dpd->setConnection();
        
        $pdfContent = base64_decode(current($dpd->getProtocol($references)));
        header('Content-Type: application/pdf');
        header('Content-Description: File Transfer');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($pdfContent));
        header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        echo $pdfContent;
        die();
    }
}