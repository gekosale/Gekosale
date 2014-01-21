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
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: order.php 619 2011-12-19 21:09:00Z gekosale $
 */
namespace Gekosale;

class OrderModel extends Component\Model
{

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
		$this->layer = $this->registry->loader->getCurrentLayer();
	}

	public function saveOrder ($Data)
	{
		$Data['clientaddress']['phone'] = $Data['contactData']['phone'];
		$Data['clientaddress']['phone2'] = $Data['contactData']['phone2'];
		$Data['clientaddress']['email'] = $Data['contactData']['email'];
		$Data['deliveryAddress']['phone'] = $Data['contactData']['phone'];
		$Data['deliveryAddress']['phone2'] = $Data['contactData']['phone2'];
		$Data['deliveryAddress']['email'] = $Data['contactData']['email'];
		
		Db::getInstance()->beginTransaction();
		try{
			$clientId = $Data['clientid'];
			if ($clientId == NULL || $clientId == 0){
				$clientId = App::getContainer()->get('session')->getActiveClientid();
			}
			$orderId = $this->addOrder($Data, $clientId);
			$this->addOrderClientData($Data['clientaddress'], $clientId, $orderId);
			$this->addOrderClientDeliveryData($Data['deliveryAddress'], $orderId);
			$this->addOrderProduct($Data['cart'], $orderId);
			App::getModel('order')->updateSessionString($orderId);
			
			$this->syncStock();
			App::getModel('giftwrap')->unsetGiftWrapData();
			
			Event::dispatch($this, 'frontend.order.saveOrder', Array(
				'id' => $orderId,
				'data' => $Data
			));
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		
		Db::getInstance()->commit();
		return $orderId;
	}

	public function updateSessionString ($id)
	{
		$sql = 'UPDATE `order` SET sessionid = :crc WHERE idorder = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('crc', session_id() . '-' . $id);
		$stmt->bindValue('id', $id);
		$stmt->execute();
	}

	protected function addOrder ($Data, $clientId = 0, $orginalOrderId = NULL)
	{
		$selectedOption = App::getContainer()->get('session')->getActiveDispatchmethodOption();
		$globalPrice = 0;
		$globalNetto = 0;
		$price = 0;
		$sql = 'INSERT INTO `order` (
					price, 
					dispatchmethodprice, 
					globalprice, 
					dispatchmethodname, 
					paymentmethodname, 
					orderstatusid,
					dispatchmethodid, 
					paymentmethodid, 
					clientid, 
					globalpricenetto, 
					viewid, 
					pricebeforepromotion, 
					currencyid, 
					currencysymbol, 
					currencyrate,
					rulescartid,
					sessionid,
					customeropinion,
					giftwrap,
					giftwrapmessage,
					paczkomat
				)
				VALUES (
					:price, 
					:dispatchmethodprice, 
					:globalprice, 
					:dispatchmethodname, 
					:paymentmethodname,
					(SELECT idorderstatus FROM orderstatus WHERE `default` = 1), 
					:dispatchmethodid, 
					:paymentmethodid, 
					:clientid, 
					:globalpricenetto, 
					:viewid, 
					:pricebeforepromotion, 
					:currencyid, 
					:currencysymbol, 
					:currencyrate,
					:rulescartid,
					:sessionid,
					:customeropinion,
					:giftwrap,
					:giftwrapmessage,
					:paczkomat
				)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('dispatchmethodprice', $Data['dispatchmethod']['dispatchmethodcost']);
		$stmt->bindValue('dispatchmethodname', $Data['dispatchmethod']['dispatchmethodname']);
		$stmt->bindValue('dispatchmethodid', $Data['dispatchmethod']['dispatchmethodid']);
		$stmt->bindValue('paymentmethodname', $Data['payment']['paymentmethodname']);
		$stmt->bindValue('paymentmethodid', $Data['payment']['idpaymentmethod']);
		$stmt->bindValue('clientid', $clientId);
		$stmt->bindValue('sessionid', session_id());
		$stmt->bindValue('customeropinion', $Data['customeropinion']);
		if (! empty($selectedOption) && $selectedOption['id'] == $Data['dispatchmethod']['dispatchmethodid']){
			$stmt->bindValue('paczkomat', $selectedOption['option']);
		}
		else{
			$stmt->bindValue('paczkomat', NULL);
		}
		$stmt->bindValue('giftwrap', (int) App::getContainer()->get('session')->getActiveGiftWrap());
		$stmt->bindValue('giftwrapmessage', App::getContainer()->get('session')->getActiveGiftWrapMessage());
		$shopCurrency = App::getContainer()->get('session')->getActiveShopCurrencyId();
		$clientCurrency = App::getContainer()->get('session')->getActiveCurrencyId();
		if ($shopCurrency !== $clientCurrency){
			$stmt->bindValue('currencyid', $clientCurrency);
			$stmt->bindValue('currencysymbol', App::getContainer()->get('session')->getActiveCurrencySymbol());
			$stmt->bindValue('currencyrate', App::getContainer()->get('session')->getActiveCurrencyRate());
		}
		else{
			$stmt->bindValue('currencyid', $shopCurrency);
			$stmt->bindValue('currencysymbol', $this->layer['currencysymbol']);
			$stmt->bindValue('currencyrate', App::getContainer()->get('session')->getActiveCurrencyRate());
		}
		
		if (isset($Data['priceWithDispatchMethodPromo']) && $Data['priceWithDispatchMethodPromo'] > 0){
			$stmt->bindValue('pricebeforepromotion', $Data['priceWithDispatchMethod']);
			if ($globalPrice == 0){
				$globalPrice = $Data['priceWithDispatchMethodPromo'];
				$globalNetto = $Data['priceWithDispatchMethodNettoPromo'];
				$price = $Data['globalPricePromo'];
			}
		}
		else{
			$stmt->bindValue('pricebeforepromotion', 0);
		}
		if ($globalPrice == 0 || $globalNetto == 0){
			$globalPrice = $Data['priceWithDispatchMethod'];
			$globalNetto = $Data['globalPriceWithoutVat'];
			$price = $Data['globalPrice'];
		}
		if (isset($Data['rulescartid']) && ! empty($Data['rulescartid'])){
			$stmt->bindValue('rulescartid', $Data['rulescartid']);
		}
		else{
			$stmt->bindValue('rulescartid', NULL);
		}
		$stmt->bindValue('globalprice', $globalPrice);
		$stmt->bindValue('globalpricenetto', $globalNetto);
		$stmt->bindValue('price', $price);
		$stmt->bindValue('viewid', Helper::getViewId());
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	protected function addOrderClientData ($Data, $clientId = 0, $orderId)
	{
		$sql = 'INSERT INTO orderclientdata SET
					firstname = AES_ENCRYPT(:firstname, :encryptionKey), 
					surname = AES_ENCRYPT(:surname, :encryptionKey), 
					street = AES_ENCRYPT(:street, :encryptionKey), 
					streetno = AES_ENCRYPT(:streetno, :encryptionKey), 
					placeno = AES_ENCRYPT(:placeno, :encryptionKey), 
					postcode = AES_ENCRYPT(:postcode, :encryptionKey), 
					place = AES_ENCRYPT(:place, :encryptionKey), 
					phone = AES_ENCRYPT(:phone, :encryptionKey),
					phone2 = AES_ENCRYPT(:phone2, :encryptionKey),
					email = AES_ENCRYPT(:email, :encryptionKey), 
					companyname = AES_ENCRYPT(:companyname, :encryptionKey), 
					nip = AES_ENCRYPT(:nip, :encryptionKey), 
					orderid = :orderid,
					clientid = :clientid,
					countryid = :country
		';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('firstname', $Data['firstname']);
		$stmt->bindValue('surname', $Data['surname']);
		$stmt->bindValue('street', $Data['street']);
		$stmt->bindValue('streetno', $Data['streetno']);
		$stmt->bindValue('placeno', $Data['placeno']);
		$stmt->bindValue('postcode', $Data['postcode']);
		$stmt->bindValue('place', $Data['placename']);
		$stmt->bindValue('phone', $Data['phone']);
		$stmt->bindValue('phone2', $Data['phone2']);
		$stmt->bindValue('email', $Data['email']);
		$stmt->bindValue('companyname', $Data['companyname']);
		$stmt->bindValue('nip', $Data['nip']);
		$stmt->bindValue('country', $Data['countryid']);
		$stmt->bindValue('orderid', $orderId);
		$stmt->bindValue('clientid', $clientId);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	protected function addOrderClientDeliveryData ($Data, $orderId)
	{
		$sql = 'INSERT INTO orderclientdeliverydata SET
					firstname = AES_ENCRYPT(:firstname, :encryptionKey), 
					surname = AES_ENCRYPT(:surname, :encryptionKey), 
					street = AES_ENCRYPT(:street, :encryptionKey), 
					streetno = AES_ENCRYPT(:streetno, :encryptionKey), 
					placeno = AES_ENCRYPT(:placeno, :encryptionKey), 
					postcode = AES_ENCRYPT(:postcode, :encryptionKey), 
					place = AES_ENCRYPT(:place, :encryptionKey), 
					phone = AES_ENCRYPT(:phone, :encryptionKey),
					phone2 = AES_ENCRYPT(:phone2, :encryptionKey),
					companyname = AES_ENCRYPT(:companyname, :encryptionKey), 
					nip = AES_ENCRYPT(:nip, :encryptionKey),  
					email = AES_ENCRYPT(:email, :encryptionKey),
					orderid = :orderid,
					countryid = :country';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('firstname', $Data['firstname']);
		$stmt->bindValue('surname', $Data['surname']);
		$stmt->bindValue('street', $Data['street']);
		$stmt->bindValue('streetno', $Data['streetno']);
		$stmt->bindValue('placeno', $Data['placeno']);
		$stmt->bindValue('postcode', $Data['postcode']);
		$stmt->bindValue('place', $Data['placename']);
		$stmt->bindValue('phone', $Data['phone']);
		$stmt->bindValue('phone2', $Data['phone2']);
		$stmt->bindValue('email', $Data['email']);
		$stmt->bindValue('companyname', $Data['companyname']);
		$stmt->bindValue('nip', NULL);
		$stmt->bindValue('orderid', $orderId);
		$stmt->bindValue('country', $Data['countryid']);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function getOrderBillingData ($idorder)
	{
		$sql = 'SELECT
					AES_DECRYPT(OCD.firstname, :encryptionkey) AS firstname,
					AES_DECRYPT(OCD.surname, :encryptionkey) AS surname,
					AES_DECRYPT(OCD.companyname, :encryptionkey) AS companyname,
					AES_DECRYPT(OCD.nip, :encryptionkey) AS nip,
					AES_DECRYPT(OCD.street, :encryptionkey) AS street,
					AES_DECRYPT(OCD.streetno, :encryptionkey) AS streetno,
					AES_DECRYPT(OCD.placeno, :encryptionkey) AS placeno,
					AES_DECRYPT(OCD.place, :encryptionkey) AS placename,
					AES_DECRYPT(OCD.postcode, :encryptionkey) AS postcode,
					AES_DECRYPT(OCD.email, :encryptionkey) AS email,
					AES_DECRYPT(OCD.phone, :encryptionkey) AS phone,
					AES_DECRYPT(OCD.phone2, :encryptionkey) AS phone2
					FROM orderclientdata OCD
				WHERE OCD.orderid = :idorder';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idorder', $idorder);
		$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data = Array(
				'firstname' => $rs['firstname'],
				'surname' => $rs['surname'],
				'companyname' => $rs['companyname'],
				'nip' => $rs['nip'],
				'street' => $rs['street'],
				'streetno' => $rs['streetno'],
				'placeno' => $rs['placeno'],
				'phone' => $rs['phone'],
				'phone2' => $rs['phone2'],
				'email' => $rs['email'],
				'placename' => $rs['placename'],
				'postcode' => $rs['postcode']
			);
		}
		return $Data;
	}

	public function getOrderShippingData ($idorder)
	{
		$sql = 'SELECT
					AES_DECRYPT(OCD.firstname, :encryptionkey) AS firstname,
					AES_DECRYPT(OCD.surname, :encryptionkey) AS surname,
					AES_DECRYPT(OCD.companyname, :encryptionkey) AS companyname,
					AES_DECRYPT(OCD.nip, :encryptionkey) AS nip,
					AES_DECRYPT(OCD.street, :encryptionkey) AS street,
					AES_DECRYPT(OCD.streetno, :encryptionkey) AS streetno,
					AES_DECRYPT(OCD.placeno, :encryptionkey) AS placeno,
					AES_DECRYPT(OCD.place, :encryptionkey) AS placename,
					AES_DECRYPT(OCD.postcode, :encryptionkey) AS postcode,
					AES_DECRYPT(OCD.email, :encryptionkey) AS email,
					AES_DECRYPT(OCD.phone, :encryptionkey) AS phone,
					AES_DECRYPT(OCD.phone2, :encryptionkey) AS phone2
					FROM orderclientdeliverydata OCD
				WHERE OCD.orderid = :idorder';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idorder', $idorder);
		$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data = Array(
				'firstname' => $rs['firstname'],
				'surname' => $rs['surname'],
				'companyname' => $rs['companyname'],
				'nip' => $rs['nip'],
				'street' => $rs['street'],
				'streetno' => $rs['streetno'],
				'placeno' => $rs['placeno'],
				'phone' => $rs['phone'],
				'phone2' => $rs['phone2'],
				'email' => $rs['email'],
				'placename' => $rs['placename'],
				'postcode' => $rs['postcode']
			);
		}
		return $Data;
	}

	protected function addOrderProduct ($Data, $orderId)
	{
		foreach ($Data as $idproduct => $product){
			if (isset($product['standard'])){
				$sql = 'INSERT INTO orderproduct(name, price, qty, qtyprice, orderid, productid, vat, pricenetto, photoid, ean)
						VALUES (:name, :price, :qty, :qtyprice, :orderid, :productid, :vat, :pricenetto, :photoid, :ean)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('name', $product['name']);
				$stmt->bindValue('price', $product['newprice']);
				$stmt->bindValue('qty', $product['qty']);
				$stmt->bindValue('qtyprice', $product['qtyprice']);
				$stmt->bindValue('orderid', $orderId);
				$stmt->bindValue('productid', $product['idproduct']);
				$stmt->bindValue('vat', $product['vat']);
				$stmt->bindValue('pricenetto', $product['pricewithoutvat']);
				$stmt->bindValue('photoid', $product['mainphotoid']);
				$stmt->bindValue('ean', $product['ean']);
				try{
					$stmt->execute();
					if ($product['trackstock'] == 1){
						$this->decreaseProductStock($product['idproduct'], $product['qty']);
					}
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
			
			if (isset($product['attributes'])){
				foreach ($product['attributes'] as $idattribute => $attribute){
					$sql = 'INSERT INTO orderproduct(name, price, qty, qtyprice, orderid, productid, productattributesetid, vat, pricenetto, photoid, ean)
							VALUES (:name, :price, :qty, :qtyprice, :orderid, :productid, :productattributesetid, :vat, :pricenetto, :photoid, :ean)';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('name', $attribute['name']);
					$stmt->bindValue('price', $attribute['newprice']);
					$stmt->bindValue('qty', $attribute['qty']);
					$stmt->bindValue('qtyprice', $attribute['qtyprice']);
					$stmt->bindValue('orderid', $orderId);
					$stmt->bindValue('productid', $attribute['idproduct']);
					$stmt->bindValue('productattributesetid', $attribute['attr']);
					$stmt->bindValue('vat', $attribute['vat']);
					$stmt->bindValue('pricenetto', $attribute['pricewithoutvat']);
					$stmt->bindValue('photoid', $attribute['mainphotoid']);
					$stmt->bindValue('ean', $attribute['ean']);
					try{
						$stmt->execute();
						$this->addOrderProductAttribute($attribute['features'], Db::getInstance()->lastInsertId());
						if ($attribute['trackstock'] == 1){
							$this->decreaseProductAttributeStock($attribute['idproduct'], $attribute['attr'], $attribute['qty']);
						}
					}
					catch (Exception $e){
						throw new Exception($e->getMessage());
					}
				}
			}
		}
	}

	public function syncStock ()
	{
		$sql = 'UPDATE product SET stock = (SELECT IF(SUM(productattributeset.stock) IS NOT NULL, SUM(productattributeset.stock), product.stock) FROM productattributeset WHERE productattributeset.productid = product.idproduct)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		
		$sql = 'UPDATE product SET enable = IF(stock > disableatstock, 1, 0) WHERE disableatstockenabled = 1';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
	}

	protected function addOrderProductAttribute ($Data, $orderProductId)
	{
		foreach ($Data as $featureid => $feature){
			$sql = 'INSERT INTO orderproductattribute (name, `group`, attributeproductvalueid, orderproductid)
					VALUES (:name, :group, :attributeproductvalueid, :orderproductid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $feature['attributename']);
			$stmt->bindValue('group', $feature['group']);
			$stmt->bindValue('attributeproductvalueid', $feature['feature']);
			$stmt->bindValue('orderproductid', $orderProductId);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	protected function decreaseProductAttributeStock ($productid, $idproductattribute, $qty)
	{
		$sql = 'UPDATE productattributeset SET stock = stock-:qty 
				WHERE productid = :productid 
				AND idproductattributeset = :idproductattribute';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('qty', $qty);
		$stmt->bindValue('productid', $productid);
		$stmt->bindValue('idproductattribute', $idproductattribute);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	protected function decreaseProductStock ($productid, $qty)
	{
		$sql = 'UPDATE product SET stock = stock-:qty
				WHERE idproduct = :idproduct';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('qty', $qty);
		$stmt->bindValue('idproduct', $productid);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function getDate ()
	{
		$orderid = App::getContainer()->get('session')->getActiveorderid();
		$signs = Array(
			':',
			'-',
			' '
		);
		$sql = "SELECT adddate FROM `order`
					WHERE idorder = :orderid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('orderid', $orderid);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = '';
		if ($rs){
			$Data = $rs['adddate'];
			$Data = str_replace($signs, '', $Data);
		}
		return $Data;
	}

	public function generateOrderLink ($idorder)
	{
		$date = $this->getDate();
		$activelink = sha1($date . $idorder);
		return $activelink;
	}

	public function changeOrderLink ($orderid, $orderlink)
	{
		$sql = "UPDATE `order` SET activelink = :activelink 
				WHERE idorder = :orderid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('activelink', $orderlink);
		$stmt->bindValue('orderid', $orderid);
		$stmt->execute();
	}

	public function getOrderInfoForEraty ($idorder)
	{
		$sql = 'SELECT
					O.idorder, 
					O.adddate as orderdate, 
					O.dispatchmethodname,
					O.paymentmethodname, 
					O.dispatchmethodprice, 
					O.globalprice, 
					O.globalpricenetto, 
					O.price,
					AES_DECRYPT(OCD.firstname, :encryptionkey) AS firstname,
					AES_DECRYPT(OCD.surname, :encryptionkey) AS surname,
					AES_DECRYPT(OCD.email, :encryptionkey) AS email,
					AES_DECRYPT(OCD.street, :encryptionkey) AS street,
					AES_DECRYPT(OCD.streetno, :encryptionkey) AS streetno,
					AES_DECRYPT(OCD.placeno, :encryptionkey) AS placeno,
					AES_DECRYPT(OCD.phone, :encryptionkey) AS phone,
					AES_DECRYPT(OCD.phone2, :encryptionkey) AS phone2,					
					AES_DECRYPT(OCD.place, :encryptionkey) AS placename,
					AES_DECRYPT(OCD.postcode, :encryptionkey) AS postcode
				FROM `order` O 
				LEFT JOIN orderclientdata OCD ON OCD.orderid= O.idorder
				WHERE O.idorder= :idorder';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idorder', $idorder);
		$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data = Array(
				'idorder' => $rs['idorder'],
				'globalprice' => $rs['globalprice'],
				'orderdate' => $rs['orderdate'],
				'price' => $rs['price'],
				'email' => $rs['email'],
				'orderdate' => $rs['orderdate'],
				'firstname' => $rs['firstname'],
				'surname' => $rs['surname'],
				'street' => $rs['street'],
				'streetno' => $rs['streetno'],
				'placeno' => $rs['placeno'],
				'phone' => $rs['phone'],
				'phone2' => $rs['phone2'],
				'placename' => $rs['placename'],
				'postcode' => $rs['postcode'],
				'dispatchmethodname' => $rs['dispatchmethodname'],
				'paymentmethodname' => $rs['paymentmethodname'],
				'dispatchmethodprice' => $rs['dispatchmethodprice']
			);
		}
		return $Data;
	}

	public function getOrderById ($id)
	{
		$sql = "SELECT
					O.clientid, 
					O.customeropinion,
					O.adddate as order_date, 
					O.idorder as order_id, 
					OS.idorderstatus as current_status_id, 
					OST.name as current_status, 
					O.dispatchmethodprice as delivererprice, 
					O.dispatchmethodname as deliverername, 
					O.dispatchmethodid, 
					O.paymentmethodid,
					O.paymentmethodname as paymentname, 
					PM.controller AS paymentmethodcontroller,
					O.price as vat_value, 
					O.globalpricenetto as totalnetto, 
					O.globalprice as total, 
					O.orderstatusid,
					V.name as view,
					O.viewid, 
					O.currencyid, 
					O.currencysymbol, 
					O.currencyrate, 
					O.rulescartid,
					O.pricebeforepromotion 
				FROM `order` O
				LEFT JOIN view V ON O.viewid = V.idview
				LEFT JOIN orderstatus OS ON OS.idorderstatus=O.orderstatusid
				LEFT JOIN orderstatustranslation OST ON OS.idorderstatus = OST.orderstatusid AND OST.languageid = :languageid
				LEFT JOIN paymentmethod PM ON PM.idpaymentmethod = O.paymentmethodid
				WHERE O.idorder = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'clientid' => $rs['clientid'],
				'customeropinion' => $rs['customeropinion'],
				'order_id' => $rs['order_id'],
				'viewid' => $rs['viewid'],
				'view' => $rs['view'],
				'orderstatusid' => $rs['orderstatusid'],
				'order_date' => $rs['order_date'],
				'current_status' => $rs['current_status'],
				'current_status_id' => $rs['current_status_id'],
				'clients_ip_address' => '123.456.123.456',
				'vat_value' => $rs['vat_value'],
				'totalnetto' => $rs['totalnetto'],
				'total' => $rs['total'],
				'currencyid' => $rs['currencyid'],
				'currencysymbol' => $rs['currencysymbol'],
				'currencyrate' => $rs['currencyrate'],
				'pricebeforepromotion' => $rs['pricebeforepromotion'],
				'rulescartid' => $rs['rulescartid'],
				'client' => $this->getClientData($id),
				'billing_address' => $this->getBillingAddress($id),
				'delivery_address' => $this->getDeliveryAddress($id),
				'products' => $this->getProducts($id)
			);
			
			$dispatchmethodVat = $this->getDispatchmethodForOrder($rs['dispatchmethodid']);
			
			$delivererpricenetto = $rs['delivererprice'] / (1 + ($dispatchmethodVat / 100));
			
			$Data['delivery_method'] = Array(
				'delivererprice' => $rs['delivererprice'],
				'deliverername' => $rs['deliverername'],
				'dispatchmethodid' => $rs['dispatchmethodid'],
				'delivererpricenetto' => $delivererpricenetto,
				'deliverervat' => sprintf('%01.2f', $dispatchmethodVat),
				'deliverervatvalue' => $rs['delivererprice'] - $delivererpricenetto
			);
			$Data['payment_method'] = Array(
				'paymentname' => $rs['paymentname'],
				'paymentmethodcontroller' => $rs['paymentmethodcontroller'],
				'paymentmethodid' => $rs['paymentmethodid']
			);
		}
		return $Data;
	}

	public function getProducts ($id)
	{
		$sql = "SELECT 
					OP.idorderproduct,
					OP.productid as id, 
					OP.productattributesetid AS variant,
					OP.name, 
					OP.pricenetto as net_price, 
					OP.qty as quantity, 
					(OP.pricenetto*OP.qty) as net_subtotal, 
					OP.vat, 
					ROUND((OP.pricenetto * OP.qty) * OP.vat/100 , 2) as vat_value,
					ROUND(((OP.pricenetto*OP.qty)*OP.vat/100 )+(OP.pricenetto*OP.qty), 2) as subtotal
				FROM orderproduct OP
				WHERE OP.orderid=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $rs['name'],
				'net_price' => $rs['net_price'],
				'quantity' => $rs['quantity'],
				'net_subtotal' => $rs['net_subtotal'],
				'vat' => $rs['vat'],
				'vat_value' => $rs['vat_value'],
				'subtotal' => $rs['subtotal'],
				'attributes' => $this->getOrderProductAttributes($rs['id'], $rs['variant'])
			);
		}
		return $Data;
	}

	public function getOrderProductAttributes ($productId, $variantId)
	{
		if ($variantId != NULL){
			$sql = '
				SELECT
					A.idproductattributeset AS id,
					A.`value`,
					A.stock AS qty,
					A.symbol,
					A.weight,
					A.suffixtypeid AS prefix_id,
					GROUP_CONCAT(SUBSTRING(CONCAT(\' \', CONCAT(AP.name,\': \',C.name)), 1) SEPARATOR \'<br />\') AS name
				FROM
					productattributeset A
					LEFT JOIN productattributevalueset B ON A.idproductattributeset = B.productattributesetid
					LEFT JOIN attributeproductvalue C ON B.attributeproductvalueid = C.idattributeproductvalue
					LEFT JOIN attributeproduct AP ON C.attributeproductid = AP.idattributeproduct
					LEFT JOIN product D ON A.productid = D.idproduct
					LEFT JOIN suffixtype E ON A.suffixtypeid = E.idsuffixtype
					LEFT JOIN vat V ON V.idvat = D.vatid
				WHERE
					productid = :productid AND
					A.idproductattributeset = :variantid
			';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $productId);
			$stmt->bindValue('variantid', $variantId);
			$stmt->execute();
			$Data = $stmt->fetchAll();
			return (isset($Data[0]) ? $Data[0] : Array());
		}
		else{
			return Array();
		}
	}

	public function getClientData ($id)
	{
		$sql = "SELECT
					CGT.name as clientgroup,
					O.clientid as ids,
					AES_DECRYPT(OCD.firstname, :encryptionKey) as firstname, 
					AES_DECRYPT(OCD.surname, :encryptionKey) as surname, 
					AES_DECRYPT(CD.email, :encryptionKey) as email
				FROM orderclientdata OCD
				LEFT JOIN `order` O ON O.idorder = OCD.orderid
				LEFT JOIN clientdata CD ON CD.clientid=O.clientid
				LEFT JOIN clientgrouptranslation CGT ON CGT.clientgroupid = CD.clientgroupid AND languageid=:languageid
				WHERE OCD.orderid=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('id', $id);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'ids' => $rs['ids'],
				'firstname' => $rs['firstname'],
				'surname' => $rs['surname'],
				'email' => $rs['email'],
				'clientgroup' => $rs['clientgroup']
			);
		}
		else{
			throw new CoreException($this->trans('ERR_CLIENT_DATA_NO_EXIST'));
		}
		return $Data;
	}

	public function getDeliveryAddress ($id)
	{
		$sql = "SELECT
					AES_DECRYPT(OCDD.firstname, :encryptionKey) firstname, 
					AES_DECRYPT(OCDD.surname, :encryptionKey) surname, 
					AES_DECRYPT(OCDD.place, :encryptionKey) city,
					AES_DECRYPT(OCDD.postcode, :encryptionKey) postcode,
					AES_DECRYPT(OCDD.phone, :encryptionKey) phone,
					AES_DECRYPT(OCDD.phone2, :encryptionKey) phone2,					
					AES_DECRYPT(OCDD.street, :encryptionKey) street,
					AES_DECRYPT(OCDD.streetno, :encryptionKey) streetno,
					AES_DECRYPT(OCDD.placeno, :encryptionKey) placeno,
					AES_DECRYPT(OCDD.email, :encryptionKey) email,
					AES_DECRYPT(OCDD.nip, :encryptionKey) nip,
					AES_DECRYPT(OCDD.companyname, :encryptionKey) companyname
				FROM orderclientdeliverydata OCDD
				WHERE orderid=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'firstname' => $rs['firstname'],
				'surname' => $rs['surname'],
				'city' => $rs['city'],
				'postcode' => $rs['postcode'],
				'phone' => $rs['phone'],
				'phone2' => $rs['phone2'],
				'street' => $rs['street'],
				'streetno' => $rs['streetno'],
				'placeno' => $rs['placeno'],
				'country' => 'Poland',
				'companyname' => $rs['companyname'],
				'email' => $rs['email'],
				'nip' => $rs['nip']
			);
		}
		else{
			throw new CoreException($this->trans('ERR_DELIVERY_ADDRESS_NO_EXIST'));
		}
		return $Data;
	}

	public function getBillingAddress ($id)
	{
		$sql = "SELECT
					AES_DECRYPT(OCD.firstname, :encryptionKey) AS firstname, 
					AES_DECRYPT(OCD.surname, :encryptionKey) AS surname, 
					AES_DECRYPT(OCD.place, :encryptionKey) AS city,
					AES_DECRYPT(OCD.postcode, :encryptionKey) AS postcode,
					AES_DECRYPT(OCD.phone, :encryptionKey) AS phone,
					AES_DECRYPT(OCD.phone2, :encryptionKey) AS phone2,					
					AES_DECRYPT(OCD.street, :encryptionKey) AS street,
					AES_DECRYPT(OCD.streetno, :encryptionKey) AS streetno,
					AES_DECRYPT(OCD.placeno, :encryptionKey) AS placeno,
					AES_DECRYPT(OCD.email, :encryptionKey) AS email,
					AES_DECRYPT(OCD.nip, :encryptionKey) AS nip,
					AES_DECRYPT(OCD.companyname, :encryptionKey) AS companyname
				FROM orderclientdata OCD
				WHERE orderid=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'firstname' => $rs['firstname'],
				'surname' => $rs['surname'],
				'city' => $rs['city'],
				'postcode' => $rs['postcode'],
				'phone' => $rs['phone'],
				'phone2' => $rs['phone2'],
				'street' => $rs['street'],
				'streetno' => $rs['streetno'],
				'placeno' => $rs['placeno'],
				'country' => 'Poland',
				'companyname' => $rs['companyname'],
				'email' => $rs['email'],
				'nip' => $rs['nip']
			);
		}
		else{
			throw new CoreException($this->trans('ERR_BILLING_ADDRESS_NO_EXIST'));
		}
		return $Data;
	}

	public function getVATAllForRangeEditor ()
	{
		$sql = 'SELECT V.idvat AS id, V.value,	VT.name 
					FROM vat V
					LEFT JOIN vattranslation VT ON VT.vatid = V.idvat AND VT.languageid = :languageid';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = $rs['value'];
		}
		return $Data;
	}

	public function getDispatchmethodForOrder ($id)
	{
		$sql = 'SELECT type FROM dispatchmethod WHERE iddispatchmethod = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$type = $rs['type'];
		}
		if ($type == 1){
			$method = $this->getDispatchmethodPrice($id);
		}
		else{
			$method = $this->getDispatchmethodWeight($id);
		}
		if (isset($method['use_vat']) && $method['use_vat'] == 1 && $method['vat'] > 0){
			$vatData = $this->getVATAllForRangeEditor();
			$vatValue = $vatData[$method['vat']];
		}
		else{
			$vatValue = 0;
		}
		return $vatValue;
	}

	public function getDispatchmethodPrice ($id)
	{
		$sql = 'SELECT iddispatchmethodprice as id, dispatchmethodcost, `from`, `to`, vat 
					FROM dispatchmethodprice
					WHERE dispatchmethodid=:id';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data['ranges'][] = Array(
				'min' => $rs['from'],
				'max' => $rs['to'],
				'price' => $rs['dispatchmethodcost']
			);
			if ($rs['vat'] > 0){
				$Data['vat'] = $rs['vat'];
				$Data['use_vat'] = 1;
			}
		}
		return $Data;
	}

	public function getDispatchmethodWeight ($id)
	{
		$sql = 'SELECT cost, `from`, `to`,vat
					FROM dispatchmethodweight
					WHERE dispatchmethodid=:id';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data['ranges'][] = Array(
				'min' => $rs['from'],
				'max' => $rs['to'],
				'price' => $rs['cost']
			);
			if ($rs['vat'] > 0){
				$Data['vat'] = $rs['vat'];
				$Data['use_vat'] = 1;
			}
		}
		return $Data;
	}

	public function getOrderProductListByClient ($idorder)
	{
		$sql = 'SELECT
					O.idorder,
					OP.name as productname,
					OP.qty,
					OP.productid,
					OP.qtyprice,
					OP.price,
					OP.pricenetto,
					OP.vat,
					OP.productid,
					OP.idorderproduct,
					PT.seo
				FROM `order` O
				LEFT JOIN orderclientdata OCD ON OCD.orderid=O.idorder
				LEFT JOIN orderproduct OP ON OP.orderid=O.idorder
				LEFT JOIN producttranslation PT ON OP.productid = PT.productid AND PT.languageid = :languageid
				WHERE O.clientid= :clientid and idorder= :idorder
				ORDER BY productname';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idorder', $idorder);
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'idproduct' => $this->isProductAvailable($rs['productid']),
				'seo' => $rs['seo'],
				'qty' => $rs['qty'],
				'productid' => $rs['productid'],
				'qtyprice' => $rs['qtyprice'],
				'price' => $rs['price'],
				'pricenetto' => $rs['pricenetto'],
				'vat' => $rs['vat'],
				'productname' => $rs['productname'],
				'idorderproduct' => $rs['idorderproduct'],
				'attributes' => $this->getProductAttributes($rs['idorderproduct'])
			);
		}
		return $Data;
	}

	public function isProductAvailable ($productid)
	{
		$sql = 'SELECT 
					idproduct
				FROM product
				WHERE idproduct = :productid';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productid', $productid);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$available = $rs['idproduct'];
		}
		else{
			$available = 0;
		}
		return $available;
	}

	public function getProductAttributes ($productid)
	{
		$sql = 'SELECT 
					OP.idorderproduct as attrId, 
					OPA.group AS attributegroup,
					OPA.name as attributename
				FROM orderproduct OP
				LEFT JOIN orderproductattribute OPA ON OPA.orderproductid = OP.idorderproduct
				WHERE orderproductid= :productid';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productid', $productid);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'attributegroup' => $rs['attributegroup'],
				'attributename' => $rs['attributename']
			);
		}
		return $Data;
	}

	public function getOrderByClient ($idorder)
	{
		$sql = 'SELECT
					OST.name as orderstatusname,
					O.idorder,
					O.adddate as orderdate,
					O.dispatchmethodname,
					O.paymentmethodname,
					O.dispatchmethodprice,
					O.globalprice,
					O.price,
					O.globalpricenetto,
					O.currencysymbol,
					O.couponcode,
					O.coupondiscount,
					O.couponfreedelivery,
					O.couponid
				FROM `order` O
				LEFT JOIN orderstatus OS ON OS.idorderstatus=O.orderstatusid
				LEFT JOIN orderstatustranslation OST ON OST.orderstatusid=OS.idorderstatus
				WHERE O.clientid= :clientid AND idorder= :idorder';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idorder', $idorder);
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		$stmt->execute();
		$rs = $stmt->fetch();
		
		if ($rs){
			$invoicedata = explode('-', $rs['orderdate']);
			$invoicedata[2] = substr($invoicedata[2], 0, 2);
			$dateinvoice = $invoicedata[0] . $invoicedata[1] . $invoicedata[2];
			
			$Data = Array(
				'idorder' => $rs['idorder'],
				'globalprice' => $rs['globalprice'],
				'price' => $rs['price'],
				'globalpricenetto' => $rs['globalpricenetto'],
				'orderstatusname' => $rs['orderstatusname'],
				'orderdate' => $rs['orderdate'],
				'currencysymbol' => $rs['currencysymbol'],
				'dispatchmethodname' => $rs['dispatchmethodname'],
				'paymentmethodname' => $rs['paymentmethodname'],
				'dispatchmethodprice' => $rs['dispatchmethodprice'],
				'dateinvoice' => $dateinvoice,
				'billingaddress' => $this->getOrderBillingData($rs['idorder']),
				'shippingaddress' => $this->getOrderShippingData($rs['idorder']),
				'invoices' => $this->getOrderInvoices($rs['idorder']),
				'couponcode' => $rs['couponcode'],
				'coupondiscount' => $rs['coupondiscount'],
				'couponid' => $rs['couponid'],
				'couponfreedelivery' => $rs['couponfreedelivery']
			);
		}
		
		return $Data;
	}

	public function getOrderInvoices ($id)
	{
		$sql = "SELECT
					idinvoice,
					symbol,
					invoicedate,
					comment,
					salesperson,
					paymentduedate,
					totalpayed
				FROM invoice
				WHERE orderid=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function getOrderListByClient ()
	{
		$sql = 'SELECT
					OST.name as orderstatusname,
					O.idorder,
					O.adddate as orderdate,
					O.dispatchmethodname,
					O.paymentmethodname,
					O.dispatchmethodprice,
					O.globalprice,
					O.price,
					O.globalpricenetto,
					O.currencysymbol,
					OSG.colour
				FROM `order` O
				LEFT JOIN orderstatus OS ON OS.idorderstatus = O.orderstatusid
				LEFT JOIN orderstatustranslation OST ON OST.orderstatusid = OS.idorderstatus
				LEFT JOIN orderstatusorderstatusgroups OSOSG ON O.orderstatusid = OSOSG.orderstatusid
				LEFT JOIN orderstatusgroups OSG ON OSG.idorderstatusgroups = OSOSG.orderstatusgroupsid
				WHERE O.clientid= :clientid ';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'idorder' => $rs['idorder'],
				'orderdate' => $rs['orderdate'],
				'orderstatusname' => $rs['orderstatusname'],
				'dispatchmethodname' => $rs['dispatchmethodname'],
				'paymentmethodname' => $rs['paymentmethodname'],
				'globalprice' => $rs['globalprice'],
				'currencysymbol' => $rs['currencysymbol'],
				'colour' => $rs['colour']
			);
		}
		return $Data;
	}

	public function getOrderStatusByEmailAndId ($email, $id)
	{
		$sql = 'SELECT
					OST.name as orderstatusname,
					O.idorder
				FROM `order` O
				LEFT JOIN orderstatus OS ON OS.idorderstatus = O.orderstatusid
				LEFT JOIN orderstatustranslation OST ON OST.orderstatusid = OS.idorderstatus
				LEFT JOIN orderclientdata OCD ON OCD.orderid = O.idorder
				WHERE AES_DECRYPT(OCD.email, :encryptionKey) = :email AND O.idorder = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('email', $email);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			return $rs['orderstatusname'];
		}
		return NULL;
	}
}