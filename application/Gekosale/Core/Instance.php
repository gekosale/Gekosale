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
 * $Id: helper.class.php 438 2011-08-27 09:29:36Z gekosale $
 */

namespace Gekosale\Core;

class Instance
{

    protected $instance = Array();

    public function __construct ()
    {
        $this->instance = $this->getInstance();
        $this->_url     = $this->instance['url'];
        $this->_user    = $this->instance['user'];
        $this->_key1    = $this->instance['key1'];
        $this->_key2    = $this->instance['key2'];
        $this->_time    = time();
    }

    protected function _mac ($value)
    {
        return hash('sha512', $value);
    }

    public function getInstance ()
    {
        $sql  = 'SELECT * FROM instance';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->execute();
        $Data = Array();
        $rs   = $stmt->fetch();
        if ($rs) {
            $Data = Array(
                'instance' => Array(
                    'id' => $rs['idinstance']
                ),
                'client'   => Array(
                    'id' => $rs['clientid']
                ),
                'limits'   => Array(
                    'products'   => $rs['products'],
                    'orders'     => $rs['orders'],
                    'clients'    => $rs['clients'],
                    'categories' => $rs['categories'],
                    'users'      => $rs['users']
                ),
                'user'     => 'overtone',
                'url'      => 'http://sb.gekosale.pl/api',
                'key1'     => $rs['authkey1'],
                'key2'     => $rs['authkey2']
            );
        }

        return $Data;
    }

    public function setEnviromentVariables ()
    {
        $client = $this->getClient();
        if (isset($client['result']['client']['billedfrom'])) {
            $exp       = $client['result']['client']['billedfrom'];
            $now       = date("Y-m-d");
            $remaining = round((strtotime($exp) - strtotime(date("Y-m-d"))) / (60 * 60 * 24));
            App::getContainer()->get('session')->setActiveAccountDaysRemaining($remaining);
        }
    }

    public function disableInstance ()
    {
        $sql  = 'UPDATE view SET offline = :offline, offlinetext = :offlinetext WHERE idview > 0';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('offline', 1);
        $stmt->bindValue('offlinetext', file_get_contents(ROOTPATH . 'design' . DS . 'instancedisabled.tpl'));
        $stmt->execute();
    }

    public function enableInstance ()
    {
        $sql  = 'UPDATE view SET offline = :offline, offlinetext = :offlinetext WHERE idview > 0';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('offline', 0);
        $stmt->bindValue('offlinetext', '');
        $stmt->execute();
    }

    protected function doApiRequest ($method, $params)
    {
        $request = array(
            'version' => '2.0',
            'id'      => 1,
            'user'    => $this->_user,
            'key1'    => $this->_key1,
            'key2'    => $this->_key2,
            'time'    => $this->_time,
            'method'  => $method,
            'params'  => Array(
                $params
            )
        );

        $request = json_encode($request);
        $curl    = curl_init($this->_url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-type: application/json'
            )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, TRUE);

        return $response;
    }

    public function getCurrentLimits ()
    {
        $sql
              = 'SELECT
					(SELECT COUNT(DISTINCT idproduct) FROM product) AS products,
					(SELECT COUNT(DISTINCT idorder) FROM `order` WHERE MONTH(adddate) = MONTH(NOW()) AND YEAR(adddate) = YEAR(NOW())) AS orders,
					(SELECT COUNT(DISTINCT idclient) FROM `client` WHERE MONTH(adddate) = MONTH(NOW()) AND YEAR(adddate) = YEAR(NOW())) AS clients,
					(SELECT COUNT(DISTINCT idcategory) FROM `category`) AS categories,
					(SELECT COUNT(DISTINCT iduser) FROM `user`) AS users,
					(SELECT COUNT(DISTINCT idview) FROM `view`) AS views
				';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->execute();
        $rs   = $stmt->fetch();
        $Data = Array();
        if ($rs) {
            $Data = Array(
                'products'   => $rs['products'],
                'orders'     => $rs['orders'],
                'clients'    => $rs['clients'],
                'categories' => $rs['categories'],
                'users'      => $rs['users'],
                'views'      => $rs['views']
            );
        }

        return $Data;
    }

    public function getInstanceMainInfo ()
    {
        $id = $this->instance['instance']['id'];

        return $this->doApiRequest('getInstanceMainInfo', $id);
    }

    public function getLimits ()
    {
        $id = $this->instance['instance']['id'];

        return $this->doApiRequest('getLimits', $id);
    }

    public function updateClientData ($Data)
    {
        return $this->doApiRequest('updateClientData', Array(
                'client' => Array(
                    'id'           => $this->instance['client']['id'],
                    'billing_data' => $Data
                )
            )
        );
    }

    public function domainCheck ($domain)
    {
        return $this->doApiRequest('checkDomain', Array(
                'domain' => $domain
            )
        );
    }

    public function addDomain ($domain)
    {
        return $this->doApiRequest('addDomain', Array(
                'domain'     => $domain,
                'instanceid' => $this->instance['instance']['id']
            )
        );
    }

    public function getDomainsForInstance ()
    {
        return $this->doApiRequest('getDomainsForInstance', $this->instance['instance']['id']);
    }

    public function checkConnection ()
    {
        return $this->doApiRequest('checkConnection', Array());
    }

    public function getPaymentSettings ()
    {
        return $this->doApiRequest('getPaymentSettings', Array());
    }

    public function getClient ()
    {
        $id = $this->instance['client']['id'];

        return $this->doApiRequest('getClient', $id);
    }

    public function getInvoicesForClient ()
    {
        $id = $this->instance['client']['id'];

        return $this->doApiRequest('getInvoicesForClient', $id);
    }

    public function getInvoice ($invoiceid)
    {
        $id = $this->instance['client']['id'];

        return $this->doApiRequest('getInvoice', Array(
                'invoiceid' => $invoiceid,
                'clientid'  => $id
            )
        );
    }

    public function addInvoice ($Data)
    {
        $id = $this->instance['client']['id'];

        return $this->doApiRequest('addInvoice', Array(
                'data'     => $Data,
                'clientid' => $id
            )
        );
    }

    public function getInvoiceBySha ($invoiceSha)
    {
        $id = $this->instance['client']['id'];

        return $this->doApiRequest('getInvoiceBySha', Array(
                'invoicesha' => $invoiceSha,
                'clientid'   => $id
            )
        );
    }

    public function confirmPayment ($invoiceid, $value)
    {
        $id = $this->instance['client']['id'];

        return $this->doApiRequest('confirmPayment', Array(
                'invoiceid' => $invoiceid,
                'value'     => $value,
                'clientid'  => $id
            )
        );
    }

}