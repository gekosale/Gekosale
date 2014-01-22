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

use SoapClient;
use Exception;

class KurjerzyModel extends Component\Model
{
	/**
	 *
	 * @todo
	 *
	 */
	const KURJERZY_API = 'https://www.kurjerzy.pl/webservice/wellcommerce';
	
	/**
	 *
	 * @todo
	 *
	 */
	protected $apiKey = '59a268f08f575f7659474c778525f640';
	protected $apiPin = '495488795621';
	
	/*
	 * protected $apiPin; protected $apiKey;
	 */
	protected $soap;

	public function __construct ($registry)
	{
		parent::__construct($registry);
		
		$settings = $this->registry->core->loadModuleSettings('kurjerzy', Helper::getViewId());
		
		if ($settings !== array()){
			$this->apiPin = $settings['apipin'];
			$this->apiKey = $settings['apikey'];
		}
		
		if (! $this->isEnabled()){
			throw new Exception('API keys Not configured');
		}
		
		$this->soap = new SoapClient(NULL, array(
			'location' => self::KURJERZY_API,
			'uri' => 'adres_klienta_api'
		));
	}

	public function isEnabled ()
	{
		return ! empty($this->apiKey) && ! empty($this->apiPin);
	}

	protected function getHash (array $data)
	{
		return md5(md5(serialize($data)) . $this->apiPin);
	}

	public function getDates ()
	{
		$datesInfo = $this->soap->getShipmentDates($this->apiKey, $this->getHash(array()), array());
		
		if (isset($datesInfo['error'])){
			throw new CoreException($datesInfo['error'][0]);
		}
		
		$dates = array();
		foreach ($datesInfo['dates'] as $time => $date){
			$dates[date('Y-m-d', (int) $time)] = $date;
		}
		
		return $dates;
	}

	public function getTypes ()
	{
		$products = $this->soap->getUserProducts($this->apiKey, $this->getHash(array()), array());
		
		$formProducts = array();
		foreach ($products as $id => $product){
			$formProducts[$id] = $product['productName'] . ', cena ' . str_replace(',', '.', $product['productPrice'] . 'zł');
		}
		
		asort($formProducts);
		
		return $formProducts;
	}

	/**
	 * Get price of shipment
	 * 
	 * @param array $shipmentData
	 *        	data loaded from shipment table
	 */
	public function priceShipment (array $shipmentRecord)
	{
		$width = (int) $shipmentRecord['width'];
		$height = (int) $shipmentRecord['height'];
		$deep = (int) $shipmentRecord['deep'];
		$weight = (int) $shipmentRecord['weight'];
		
		if ($width <= 0)
			$width = 25;
		
		if ($height <= 0)
			$height = 45;
		
		if ($deep <= 0)
			$deep = 35;
		
		if ($weight <= 0)
			$weight = 20;
		
		$date = strtotime($shipmentRecord['order']['shipments'][0]['shipmentdate']);
		
		$parcel = array();
		$parcel[0]['weight'] = $weight;
		$parcel[0]['height'] = $height;
		$parcel[0]['width'] = $width;
		$parcel[0]['length'] = $deep;

		$instance = new Instance();
		$contactData = $instance->getClient();
		$contactData = $contactData['result']['client'];
		
		$productInfo = preg_split('~[\r\n]+~', $shipmentRecord['comment'], 3);
		
		$orderData = array();
		$orderData['order'][0]['parcel'] = $parcel;
		
		$orderData['order'][0]['details']['content'] = substr($productInfo[2], 0, 50);
		$orderData['order'][0]['details']['order_value'] = $shipmentRecord['amount'];
		
		// szczegoly odbioru
		$orderData['order'][0]['shipment']['date'] = (string) $date;
		$orderData['order'][0]['shipment']['time_from'] = (string) 11;
		$orderData['order'][0]['shipment']['time_to'] = (string) 17;
				
		$orderData['order'][0]['service']['product_id'] = $shipmentRecord['shipmenttype'];
				
		// dane nadawcy
		$orderData['order'][0]['sender']['name'] = $contactData['companyname'];
		$orderData['order'][0]['sender']['street'] = $contactData['street'];
		$orderData['order'][0]['sender']['housenr'] = rtrim($contactData['streetno'] . ' ' . $contactData['placeno']);
		$orderData['order'][0]['sender']['postcode'] = $contactData['postcode'];
		$orderData['order'][0]['sender']['city'] = $contactData['city'];
		$orderData['order'][0]['sender']['phone'] = $contactData['phone'];
		$orderData['order'][0]['sender']['email'] = $contactData['email'];
		
		// dane odbiorcy
		$orderData['order'][0]['recipient']['name'] = $shipmentRecord['order']['delivery_address']['firstname'] . ' ' . $shipmentRecord['order']['delivery_address']['surname'];
		$orderData['order'][0]['recipient']['street'] = $shipmentRecord['order']['delivery_address']['street'];
		$orderData['order'][0]['recipient']['housenr'] = $shipmentRecord['order']['delivery_address']['streetno'] . ' ' . $shipmentRecord['order']['delivery_address']['placeno'];
		$orderData['order'][0]['recipient']['postcode'] = $shipmentRecord['order']['delivery_address']['postcode'];
		$orderData['order'][0]['recipient']['city'] = $shipmentRecord['order']['delivery_address']['city'];
		$orderData['order'][0]['recipient']['phone'] = $shipmentRecord['order']['delivery_address']['phone'];
		$orderData['order'][0]['recipient']['email'] = $shipmentRecord['order']['delivery_address']['email'];
		
		$order = $this->soap->makeOrder($this->apiKey, $this->getHash($orderData), $orderData);
		
		if (isset($order['error'])){
			throw new Exception(implode(', ', $order['error']));
		}
		
		if (! isset($order['order'][0])){
			throw new Exception('Wystąpił nieznany błąd');
		}
		
		$sql = "UPDATE shipment SET dispatchernumber = :dispatchernumber WHERE idshipment = :idshipment";
		
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('dispatchernumber', $order['order'][0]);
		$stmt->bindValue('idshipment', $shipmentRecord['idshipment']);
		$stmt->execute();
		
		$products = $this->soap->getUserProducts($this->apiKey, $this->getHash(array()), array());
		
		return array(
			'price' => $productprice = str_replace(',', '.', $products[$shipmentRecord['shipmenttype']]['productPrice']) + $shipmentRecord['amount'],
			'shipmentdate' => $shipmentRecord['order']['shipments'][0]['shipmentdate']
		);
	}
}