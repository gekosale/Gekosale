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
use SoapClient;
use Exception;
use ElektronicznyNadawca;
use paczkaPocztowaType;
use przesylkaBiznesowaPlusType;
use przesylkaBiznesowaType;
use adresType;
use hello;
use helloResponse;
use kategoriaType;
use addShipment;
use gabarytType;
use sendEnvelope;
use clearEnvelope;
use getEnvelopeStatus;
use getEnvelopeBufor;
use getUrzedyNadania;
use getAddresLabelByGuid;
use gabarytBiznesowaType;
use terminRodzajPlusType;
use getUrzedyWydajaceEPrzesylki;
use pobranieType;
use getEnvelopeContentFull;
use getOutboxBook;
use getAddressLabel;
use addressLabelContent;
use getAddressLabelResponse;
use getFirmowaPocztaBook;
use getPasswordExpiredDate;

include_once ROOTPATH . 'lib' . DS . 'ElektronicznyNadawca' . DS . 'ElektronicznyNadawca-prod.php';

class ElektronicznyNadawcaModel extends Component\Model
{

    protected $settings;

    public $title = 'ElektronicznyNadawca';

    public $module = 'elektronicznynadawca';

    CONST TAB_NAME = 'elektronicznynadawca_data';

    public function __construct ($registry)
    {
        parent::__construct($registry);
        
        $this->settings = $this->registry->core->loadModuleSettings('elektronicznynadawca', Helper::getViewId());
        
        if (! empty($this->settings)){
            try{
                $this->elektronicznyNadawca = new ElektronicznyNadawca(ROOTPATH . 'lib' . DS . 'ElektronicznyNadawca' . DS . 'en.wsdl', Array(
                    'login' => $this->settings['elektronicznynadawcalogin'],
                    'password' => $this->settings['elektronicznynadawcapassword'],
                    'exceptions' => false,
                    'trace' => 1
                ));
            }
            catch (\SoapFault $e){
                throw new \Exception($e->getMessage());
            }
            catch (Exception $e){
                throw new CoreException($e->getMessage());
            }
        }
    }

    public function addFields ($event, $request)
    {
        $form = &$request['form'];
        
        $elektronicznynadawca = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => self::TAB_NAME,
            'label' => 'Integracja z ElektronicznyNadawca'
        )));
        
        $elektronicznynadawca->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'elektronicznynadawcalogin',
            'label' => 'Login'
        )));
        
        $elektronicznynadawca->AddChild(new FormEngine\Elements\Password(Array(
            'name' => 'elektronicznynadawcapassword',
            'label' => 'Hasło'
        )));
        
        $settings = $this->registry->core->loadModuleSettings('elektronicznynadawca', (int) $request['id']);
        
        if (! empty($settings)){
            $populate = Array(
                'elektronicznynadawca_data' => Array(
                    'elektronicznynadawcalogin' => $settings['elektronicznynadawcalogin'],
                    'elektronicznynadawcapassword' => $settings['elektronicznynadawcapassword']
                )
            );
            
            $event->setReturnValues($populate);
        }
    }

    public function saveSettings ($request)
    {
        $Settings = Array(
            'elektronicznynadawcalogin' => $request['data']['elektronicznynadawcalogin'],
            'elektronicznynadawcapassword' => $request['data']['elektronicznynadawcapassword']
        );
        
        $this->registry->core->saveModuleSettings('elektronicznynadawca', $Settings, $request['id']);
    }

    public function getPopulateData ()
    {
        $Data = Array(
            'shipment_data' => Array(
                'urzad_nadania' => $this->getUrzedyNadania(),
                'shipmenttype' => $this->getTypes(),
                'gabaryt' => $this->getGabaryt(),
                'codamount' => 0,
                'comment' => $this->trans('TXT_SHIPMENT_ADDED_EN')
            )
        );
        
        return $Data;
    }

    public function checkEnvelope ()
    {
        $response = $this->elektronicznyNadawca->getEnvelopeBufor(new getEnvelopeBufor());
        
        $buffer = current($response);
        
        $sql = "SELECT COUNT(*) AS total FROM shipments WHERE DAY(adddate) = DAY(NOW()) AND MONTH(adddate) = MONTH(NOW()) AND YEAR(adddate) = YEAR(NOW()) AND model = :model";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('model', $this->module);
        $stmt->execute();
        $rs = $stmt->fetch();
        
        if ($rs['total'] == 0 && count($buffer) > 0){
            $this->elektronicznyNadawca->clearEnvelope(new clearEnvelope());
        }
    }

    protected function _maxLength ($length, $value, $valueName)
    {
        if (strlen($value) <= $length){
            return $value;
        }
        App::getContainer()->get('session')->setVolatileMessage('Wartość: ' . $valueName . ' jest zbyt długa. Dopuszczalna ilość znaków to: ' . $value);
        return App::redirect(__ADMINPANE__ . '/shipment');
    }

    protected function _parseNum ($value)
    {
        return preg_replace('/[^0-9]*/', '', $value);
    }

    public function addShipment ($formData, $orderData)
    {
        set_time_limit(0);
        
        $this->checkEnvelope();
        
        $storeData = App::getModel('invoice')->getMainCompanyAddress($orderData['viewid']);
        
        $tmp = new addShipment();
        
        $A = new adresType();
        //EN ODBC specs
        //https://e-nadawca.poczta-polska.pl
        $A->nazwa = $this->_maxLength(30, $orderData['delivery_address']['firstname'], 'Nazwa');
        $A->nazwa2 = $this->_maxLength(30, $orderData['delivery_address']['surname'], 'Nazwa 2');
        $A->ulica = $this->_maxLength(35, $orderData['delivery_address']['street'], 'Ulica');
        $A->numerDomu = $this->_maxLength(11, $orderData['delivery_address']['streetno'], 'Numer domu');
        $A->numerLokalu = $this->_maxLength(11, $orderData['delivery_address']['placeno'], 'Numer lokalu');
        $A->miejscowosc = $this->_maxLength(30, $orderData['delivery_address']['city'], 'Miejscowość');
        $A->kodPocztowy = $this->_maxLength(5, $this->_parseNum($orderData['delivery_address']['postcode']), 'Kod pocztowy');
        $A->kraj = App::getModel('shipment')->getCountryName($orderData['delivery_address']['countryid']);
        $phone = App::getModel('shipment')->parseNumber($orderData['delivery_address']['phone']);
        if (strlen($phone) == 9){
            $A->mobile = $this->_maxLength(9, $phone, 'Telefon komórkowy');
        }
        $A->telefon = $this->_maxLength(9, $this->_parseNum($orderData['delivery_address']['phone2']), 'Telefon');
        $A->email = $this->_maxLength(50, $orderData['delivery_address']['email'], 'E-mail');
        
        $guid = App::getModel('shipment')->getGuid();
        
        $P = new przesylkaBiznesowaType();
        $P->posteRestante = 0;
        // $P->masa = 2250; // masa w gramach
        $P->gabaryt = $this->getGabarytObject($formData['gabaryt']);
        $P->kwotaTranzakcji = $formData['codamount'];
        if ($formData['codamount'] > 0){
            $pobranie = new pobranieType();
            $pobranie->sposobPobrania = 'RACHUNEK_BANKOWY';
            $pobranie->kwotaPobrania = str_replace(',', '.', $formData['codamount']) * 100;
            $nbrValid = false;
            if (isset($storeData['banknr'])){
                $nbrClearStr = preg_replace('/[^0-9]*/i', '', $storeData['banknr']);
                if (App::getModel('payment/banktransfer')->validateIbanNumber($storeData['banknr']) || App::getModel('payment/banktransfer')->validateIbanNumber($nbrClearStr)){
                    $pobranie->nrb = $nbrClearStr;
                    $nbrValid = true; //ok...
                }
            }
            if (! $nbrValid){
                App::getContainer()->get('session')->setVolatileMessage('Podany numer konta bankowego jest nieprawidłowy.');
                return App::redirect(__ADMINPANE__ . '/shipment');
            }
            $pobranie->tytulem = 'Zamówienie ' . $orderData['order_id'];
            $P->pobranie = $pobranie;
        }
        $P->ostroznie = false;
        $P->kategoria = kategoriaType::PRIORYTETOWA; // * sztywno
        $P->iloscPotwierdzenOdbioru = 1;
        $P->eSposobPowiadomieniaAdresata = 'SMS'; // sms
        $P->numerPrzesylkiKlienta = $guid;
        $P->iloscDniOczekiwaniaNaWydanie = 7;
        $P->terminRodzajPlus = terminRodzajPlusType::STANDARD;
        $P->adres = $A;
        $P->guid = $guid;
        $tmp->przesylki[] = $P;
        $response = current($this->elektronicznyNadawca->addShipment($tmp));
        
        $dispatcherNumber = $response->numerNadania;
        
        if (strlen($dispatcherNumber) > 0){
            $sql = "INSERT INTO shipments SET
						orderid = :orderid,
						guid = :guid,
						packagenumber = :packagenumber,
						label = :label,
						adddate = :adddate,
						orderdata = :orderdata,
						formdata = :formdata,
						model = :model";
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('orderid', $orderData['order_id']);
            $stmt->bindValue('guid', $guid);
            $stmt->bindValue('packagenumber', $dispatcherNumber);
            $stmt->bindValue('label', base64_encode($this->getLabel($guid)));
            $stmt->bindValue('adddate', date('Y-m-d H:i:s'));
            $stmt->bindValue('orderdata', serialize($orderData));
            $stmt->bindValue('formdata', serialize($formData));
            $stmt->bindValue('model', $this->module);
            $stmt->execute();
            
            $status = $formData['orderstatus'];
            $sql = 'UPDATE `order` SET orderstatusid = :status WHERE idorder = :id';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('status', $status);
            $stmt->bindValue('id', $orderData['order_id']);
            $stmt->execute();
            
            App::getModel('order')->addOrderHistory(Array(
                'comment' => str_replace('{PACKAGE_NUMBER}', $dispatcherNumber, $formData['comment']),
                'inform' => $formData['notifyuser'],
                'status' => $status
            ), $orderData['order_id']);
            
            if ($formData['notifyuser'] == 1){
                $order = App::getModel('order')->getOrderById((int) $orderData['order_id']);
                App::getModel('order')->notifyUser($order, $formData['orderstatus']);
            }
        }
        else{
            \Gekosale\Arr::debug($response);
        }
        
        return $dispatcherNumber;
    }

    public function getProtocol ($references)
    {
        set_time_limit(0);
        
        $Data = Array();
        foreach ($references as $reference){
            $sql = 'SELECT
						*
					FROM shipments WHERE guid = :guid AND envelopeid IS NULL';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('guid', $reference);
            $stmt->execute();
            $rs = $stmt->fetch();
            if ($rs){
                $unserialize = unserialize($rs['formdata']);
                $Data[$unserialize['urzad_nadania']][] = $reference;
            }
        }
        
        if (count($Data) > 0){
            foreach ($Data as $un => $guids){
                
                $EN = new sendEnvelope();
                $EN->urzadNadania = $un;
                $EN->pakiet = Array();
                
                $responseData = $this->elektronicznyNadawca->sendEnvelope($EN);
                
                $response = json_decode(json_encode($responseData), true);
                
                if (isset($response['idEnvelope'])){
                    $envelopeId = $response['idEnvelope'];
                    
                    foreach ($guids as $guid){
                        $sql = 'UPDATE `shipments` SET envelopeid = :envelopeid WHERE guid = :guid';
                        $stmt = Db::getInstance()->prepare($sql);
                        $stmt->bindValue('guid', $guid);
                        $stmt->bindValue('envelopeid', $envelopeId);
                        $stmt->execute();
                    }
                }
                elseif (isset($response['error']['errorNumber']) && $response['error']['errorNumber'] == 11114){
                    App::getContainer()->get('session')->setVolatileMessage('Zbiór został już wysłany.');
                    return App::redirect(__ADMINPANE__ . '/shipment');
                }
                else{
                    \Gekosale\Arr::debug($responseData);
                }
                break;
            }
        }
        
        /*
         * Pobranie envelopeid dla paczek
         */
        
        $Data = Array();
        foreach ($references as $reference){
            $sql = 'SELECT
						*
					FROM shipments WHERE guid = :guid AND envelopeid IS NOT NULL';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('guid', $reference);
            $stmt->execute();
            $rs = $stmt->fetch();
            if ($rs){
                $Data[$rs['envelopeid']][] = $reference;
            }
        }
        
        foreach ($Data as $envelopeid => $guids){
            $EN = new getFirmowaPocztaBook();
            $EN->idEnvelope = $envelopeid;
            $response = current($this->elektronicznyNadawca->getFirmowaPocztaBook($EN));
            header('Content-Type: application/pdf');
            header('Content-Length: ' . strlen($response));
            header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0');
            header('Expires: 0');
            echo $response;
            die();
        }
    }

    public function getLabel ($guid)
    {
        $AL = new getAddresLabelByGuid();
        $AL->guid = $guid;
        $pdfResponse = current($this->elektronicznyNadawca->getAddresLabelByGuid($AL));
        return current($pdfResponse);
    }

    public function getPdfContentByGuid ($guid)
    {
        $AL = new getAddresLabelByGuid();
        $AL->guid = $guid;
        $pdfResponse = current($this->elektronicznyNadawca->getAddresLabelByGuid($AL));
        return current($pdfResponse);
    }

    public function test ()
    {
        $response = $this->elektronicznyNadawca->getEnvelopeBufor(new getEnvelopeBufor());
    }

    public function getUrzedyNadania ()
    {
        $response = current($this->elektronicznyNadawca->getUrzedyNadania(new getUrzedyNadania()));
        if ($response == 'Authorization Required'){
            App::getContainer()->get('session')->setVolatileMessage('Podałeś niepoprawny login/hasło lub hasło wygasło. Sprawdź konfigurację modułu ' . $this->title);
            App::redirect(__ADMINPANE__ . '/view/edit/' . Helper::getViewId() . '#' . self::TAB_NAME);
        }
        if (is_array($response)){
            foreach ($response as $urzad){
                $formProducts[$urzad->urzadNadania] = $urzad->nazwaWydruk;
            }
        }
        else{
            $formProducts[$response->urzadNadania] = $response->nazwaWydruk;
        }
        
        return $formProducts;
    }

    public function getTypes ()
    {
        $formProducts = array();
        $formProducts['EKONOMICZNA'] = 'EKONOMICZNA';
        $formProducts['PRIORYTETOWA'] = 'PRIORYTETOWA';
        return $formProducts;
    }

    public function getGabarytObject ($gabarytType)
    {
        switch ($gabarytType) {
            case 'XXL':
                return gabarytBiznesowaType::XXL;
                break;
            case 'XS':
                return gabarytBiznesowaType::XS;
                break;
            case 'S':
                return gabarytBiznesowaType::S;
                break;
            case 'M':
                return gabarytBiznesowaType::M;
                break;
            case 'L':
                return gabarytBiznesowaType::L;
                break;
            case 'XL':
                return gabarytBiznesowaType::XL;
                break;
        }
    }

    public function getGabaryt ()
    {
        $formProducts = array();
        $formProducts['XS'] = 'XS';
        $formProducts['S'] = 'S';
        $formProducts['M'] = 'M';
        $formProducts['L'] = 'L';
        $formProducts['XL'] = 'XL';
        $formProducts['XXL'] = 'XXL';
        return $formProducts;
    }
}