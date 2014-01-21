<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 *
 * $Revision: 309 $
 * $Author: gekosale $
 * $Date: 2011-08-01 21:10:16 +0200 (Pn, 01 sie 2011) $
 * $Id: invoice.php 309 2011-08-01 19:10:16Z gekosale $
 */
namespace Gekosale;
use FormEngine;
use SoapClient;

class WfirmaModel extends Component\Model
{

    protected $wFirmaSessionId = NULL;

    protected $uri = 'http://api.wfirma.pl/';

    protected $location = 'http://api.wfirma.pl/';

    protected $trace = 1;

    protected $connection = NULL;

    protected $instance = NULL;

    public function __construct ($registry, $modelFile)
    {
        parent::__construct($registry, $modelFile);
    }

    public function addFields ($event, $request)
    {
        $form = &$request['form'];
        
        $wfirma = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'wfirma_data',
            'label' => 'Integracja z wFirma'
        )));
        
        $wfirma->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'wfirmalogin',
            'label' => 'Login do wFirma'
        )));
        
        $wfirma->AddChild(new FormEngine\Elements\Password(Array(
            'name' => 'wfirmapassword',
            'label' => 'Hasło do wFirma'
        )));
        
        $settings = $this->registry->core->loadModuleSettings('wfirma', Helper::getViewId());
        
        if (! empty($settings)){
            $populate = Array(
                'wfirma_data' => Array(
                    'wfirmalogin' => $settings['wfirmalogin'],
                    'wfirmapassword' => $settings['wfirmapassword']
                )
            );
            
            $event->setReturnValues($populate);
        }
    }

    public function saveSettings ($request)
    {
        $Settings = Array(
            'wfirmalogin' => $request['data']['wfirmalogin'],
            'wfirmapassword' => $request['data']['wfirmapassword']
        );
        $this->registry->core->saveModuleSettings('wfirma', $Settings, $request['id']);
    }

    public function login ($user, $password)
    {
        $this->connection = new SoapClient(null, array(
            'uri' => $this->uri,
            'location' => $this->location,
            'trace' => $this->trace
        ));
        
        $this->instance = $this->connection->login($user, $password);
        if ($this->instance['status'] == 'OK'){
            $this->wFirmaSessionId = $this->instance['response'];
        }
        else{
            die('Error.');
        }
    }

    public function addInvoice ($Data, $orderId, $invoiceTypeId, $orderData)
    {
        $settings = $this->registry->core->loadModuleSettings('wfirma', $orderData['viewid']);
        
        $this->login($settings['wfirmalogin'], $settings['wfirmapassword']);
        
        switch ($invoiceTypeId) {
            case 1:
                $series = 'proforma';
                $type = 'proforma';
                break;
            case 2:
                $series = 'normal';
                $type = 'normal';
                break;
        }
        
        switch ($orderData['payment_method']['paymentmethodcontroller']) {
            case 'ondelivery':
            case 'pickup':
                $sposob_platnosci = 'cash';
                break;
            case 'banktransfer':
                $sposob_platnosci = 'transfer';
                break;
            default:
                $sposob_platnosci = 'transfer';
        }
        
        $invoiceHeader = array(
            'date' => $Data['invoicedate'],
            'disposaldate' => date('Y-m-d', strtotime($orderData['order_date'])),
            'paymentdate' => $Data['duedate'],
            'paymentmethod' => $sposob_platnosci,
            'paid' => ($Data['totalpayed'] == $orderData['vat_value']) ? 1 : $Data['totalpayed'],
            'description' => substr($Data['comment'], 0, 320),
            'auto_send' => '0',
            'lump' => 'rate20',
            'tax_evaluation_method' => 'netto',
            'series' => $series,
            'type' => $type
        );
        
        $nip = str_replace(Array(
            '-',
            ' '
        ), '', $orderData['billing_address']['nip']);
        
        // dane nabywcy
        $contractorDetails = array(
            'name' => (strlen($orderData['billing_address']['companyname']) > 0) ? $orderData['billing_address']['companyname'] : $orderData['billing_address']['firstname'] . ' ' . $orderData['billing_address']['surname'],
            'nip' => (strlen($orderData['billing_address']['nip']) > 0) ? $nip : '',
            'street' => $orderData['billing_address']['street'] . ' ' . $orderData['billing_address']['streetno'] . ' ' . (($orderData['billing_address']['placeno'] != '') ? '/' . $orderData['billing_address']['placeno'] : ''),
            'zip' => $orderData['billing_address']['postcode'],
            'city' => $orderData['billing_address']['city'],
            'email' => $orderData['billing_address']['email'],
            'add' => 1
        );
        
        if ($orderData['pricebeforepromotion'] > 0 && ($orderData['pricebeforepromotion'] < $orderData['total'])){
            $rulesCostGross = $orderData['total'] - $orderData['pricebeforepromotion'];
            $rulesCostNet = ($orderData['total'] - $orderData['pricebeforepromotion']) / (1 + ($orderData['delivery_method']['deliverervat'] / 100));
            $rulesVat = $rulesCostGross - $rulesCostNet;
            $orderData['products'][] = Array(
                'name' => $orderData['delivery_method']['deliverername'],
                'net_price' => sprintf('%01.2f', $orderData['delivery_method']['delivererpricenetto'] + $rulesCostNet),
                'quantity' => 1,
                'net_subtotal' => sprintf('%01.2f', $orderData['delivery_method']['delivererpricenetto'] + $rulesCostNet),
                'vat' => sprintf('%01.2f', $orderData['delivery_method']['deliverervat']),
                'vat_value' => sprintf('%01.2f', $orderData['delivery_method']['deliverervatvalue'] + $rulesVat),
                'subtotal' => sprintf('%01.2f', $orderData['delivery_method']['delivererprice'] + $rulesCostGross),
                'lp' => $lp
            );
        }
        else{
            $orderData['products'][] = Array(
                'name' => $orderData['delivery_method']['deliverername'],
                'net_price' => sprintf('%01.2f', $orderData['delivery_method']['delivererpricenetto']),
                'quantity' => 1,
                'net_subtotal' => sprintf('%01.2f', $orderData['delivery_method']['delivererpricenetto']),
                'vat' => sprintf('%01.2f', $orderData['delivery_method']['deliverervat']),
                'vat_value' => sprintf('%01.2f', $orderData['delivery_method']['deliverervatvalue']),
                'subtotal' => sprintf('%01.2f', $orderData['delivery_method']['delivererprice'])
            );
        }
        
        $invoiceContents = Array();
        foreach ($orderData['products'] as $key => $val){
            $invoiceContents[] = Array(
                'name' => $val['name'],
                'price' => $val['net_price'],
                'unit' => 'szt.',
                'count' => $val['quantity'],
                'vatcode' => round($val['vat'], 0),
                'classification' => ''
            );
        }
        
        $r = $this->connection->addInvoice($this->wFirmaSessionId, $invoiceHeader, $contractorDetails, $invoiceContents);
        
        if ($r['status'] == 'OK'){
            $invoiceId = $r['response'];
            $contentOriginalHtml = $this->downloadInvoiceOriginal($invoiceId);
            $contentCopyHtml = $this->downloadInvoiceCopy($invoiceId);
            
            $sql = "INSERT INTO invoice SET
						symbol = :symbol,
						invoicedate = :invoicedate,
						salesdate = :salesdate,
						paymentduedate = :paymentduedate,
						salesperson = :salesperson,
						invoicetype = :invoicetype,
						comment = :comment,
						contentoriginal = :contentoriginal,
						contentcopy = :contentcopy,
						orderid = :orderid,
						totalpayed = :totalpayed,
						externalid = :externalid,
						contenttype = :contenttype,
						viewid = :viewid";
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('symbol', $r['data']['Invoice']['fullnumber']);
            $stmt->bindValue('invoicedate', $Data['invoicedate']);
            $stmt->bindValue('salesdate', date('Y-m-d', strtotime($orderData['order_date'])));
            $stmt->bindValue('paymentduedate', $Data['duedate']);
            $stmt->bindValue('salesperson', $Data['salesperson']);
            $stmt->bindValue('invoicetype', $invoiceTypeId);
            $stmt->bindValue('comment', $Data['comment']);
            $stmt->bindValue('contentoriginal', base64_encode($contentOriginalHtml));
            $stmt->bindValue('contentcopy', base64_encode($contentCopyHtml));
            $stmt->bindValue('orderid', $orderId);
            $stmt->bindValue('totalpayed', $Data['totalpayed']);
            $stmt->bindValue('viewid', $orderData['viewid']);
            $stmt->bindValue('externalid', $invoiceId);
            $stmt->bindValue('contenttype', 'pdf');
            
            try{
                $stmt->execute();
            }
            catch (Exception $e){
                throw new Exception($e->getMessage());
            }
            
            $this->sendInvoice($invoiceId, $orderData['billing_address']['email'], $orderId);
            $this->connection->logout($this->wFirmaSessionId);
        }
        else{
            print_r($r['status']);
            print_r($r['response']);
        }
    }

    protected function sendInvoice ($invoiceId, $email, $orderId)
    {
        $options = array(
            'subject' => 'Faktura do zamówienia ' . $orderId,
            'page' => 'invoice',
            'leaflet' => 0,
            'duplicate' => 0
        );
        
        $response = $this->connection->sendInvoice($this->wFirmaSessionId, $invoiceId, $email);
    }

    protected function downloadInvoiceOriginal ($invoiceId)
    {
        $options = array(
            'page' => 'invoice',
            'leaflet' => 0,
            'duplicate' => 0
        );
        
        $response = $this->connection->downloadInvoice($this->wFirmaSessionId, $invoiceId, $options);
        if ($response['status'] == 'OK'){
            return $this->getPdf($response['response']);
        }
        return NULL;
    }

    protected function downloadInvoiceCopy ($invoiceId)
    {
        $options = array(
            'page' => 'invoicecopy',
            'leaflet' => 0,
            'duplicate' => 0
        );
        
        $response = $this->connection->downloadInvoice($this->wFirmaSessionId, $invoiceId, $options);
        if ($response['status'] == 'OK'){
            return $this->getPdf($response['response']);
        }
        return NULL;
    }

    protected function getPdf ($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $Data = curl_exec($ch);
        curl_close($ch);
        return $Data;
    }
}