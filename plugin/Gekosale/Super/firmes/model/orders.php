<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2011 Gekosale. Zabronione jest usuwanie informacji o
 * licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 279 $
 * $Author: gekosale $
 * $Date: 2011-07-28 23:13:43 +0200 (Cz, 28 lip 2011) $
 * $Id: product.php 279 2011-07-28 21:13:43Z gekosale $
 */
namespace Gekosale;

class OrdersModel extends Component\Model
{

	public function getOrderById ($id)
	{
		$sql = "SELECT
					IF(O.clientid IS NULL, 0, O.clientid) AS clientid,
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
					O.price as vat_value,
					O.globalpricenetto as totalnetto,
					O.globalprice as total,
					O.globalqty,
					O.currencyid,
					O.currencysymbol,
					IF(O.currencyrate IS NULL, 1, O.currencyrate) AS currencyrate,
					O.viewid,
					O.firmesid,
					DM.subiektsymbol
				FROM `order` O
				LEFT JOIN view V ON O.viewid = V.idview
				LEFT JOIN dispatchmethod DM ON DM.iddispatchmethod = O.dispatchmethodid
				LEFT JOIN orderstatus OS ON OS.idorderstatus=O.orderstatusid
				LEFT JOIN orderstatustranslation OST ON OS.idorderstatus = OST.orderstatusid AND OST.languageid = :languageid
				LEFT JOIN paymentmethod PM ON PM.idpaymentmethod = O.paymentmethodid
				WHERE O.idorder=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data['header'] = Array(
				'orderid' => $rs['order_id'],
				'subiektdokid' => $rs['firmesid'],
				'orderdate' => date('c', strtotime($rs['order_date'])),
				'clientid' => $rs['clientid'],
				'currencysymbol' => $rs['currencysymbol'],
				'currencyrate' => $rs['currencyrate'],
				'orderstatusid' => $rs['current_status_id'],
				'orderstatusname' => $rs['current_status'],
				'comments' => $rs['customeropinion'],
				'viewid' => $rs['viewid']
			);
			$Data['client'] = $this->getClientData($id);
			$Data['billing'] = $this->getBillingAddress($id);
			$Data['shipping'] = $this->getDeliveryAddress($id);
			$Data['products'] = $this->getProducts($id);
			$dispatchmethodVat = $this->getDispatchmethodForOrder($rs['dispatchmethodid']);
			
			$delivererpricenetto = $rs['delivererprice'] / (1 + ($dispatchmethodVat / 100));
			
			$Data['footer']['delivery'] = Array(
				'name' => $rs['deliverername'],
				'symbol' => $rs['subiektsymbol'],
				'net_price' => $delivererpricenetto,
				'quantity' => 1,
				'net_subtotal' => $delivererpricenetto,
				'vat' => sprintf('%01.2f', $dispatchmethodVat),
				'vat_value' => $rs['delivererprice'] - $delivererpricenetto,
				'subtotal' => $rs['delivererprice']
			);
			
			$Data['footer']['payment'] = Array(
				'name' => 'Pobranie',
				'net_price' => '10.00',
				'quantity' => 1,
				'net_subtotal' => '10.00',
				'vat' => '23.00',
				'vat_value' => '2.30',
				'subtotal' => '12.30'
			);
			return $Data;
		}
	}

	public function getOrderList ($request)
	{
		$limit = (isset($request['limit'])) ? $request['limit'] : 100;
		$offset = (isset($request['offset'])) ? $request['offset'] : 0;
		$startingfrom = (isset($request['starting_from'])) ? $request['starting_from'] : 0;
		
		$sql = "SELECT 
					O.idorder, 
					O.adddate as orderdate,
					O.firmesid
				FROM `order` O 
				LEFT JOIN orderclientdata OCD ON OCD.orderid= O.idorder
				WHERE O.idorder > :starting_from
				LIMIT {$offset},{$limit}";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('starting_from', $startingfrom);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data['list'][] = Array(
				'id' => $rs['idorder'],
				'date' => $rs['orderdate'],
				'subiektdokid' => $rs['firmesid']
			);
		}
		$Data['params'] = Array(
			'limit' => $limit,
			'offset' => $offset,
			'starting_from' => $startingfrom
		);
		return $Data;
	}

	public function getClientData ($id)
	{
		$sql = "SELECT
					CGT.name as clientgroup,
					IF(O.clientid IS NULL, 0, O.clientid) AS id, 
					AES_DECRYPT(OCD.firstname, :encryptionKey) as firstname, 
					AES_DECRYPT(OCD.surname, :encryptionKey) as surname, 
					AES_DECRYPT(CD.email, :encryptionKey) as email,
					CD.clientgroupid
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
				'id' => $rs['id'],
				'firstname' => $rs['firstname'],
				'surname' => $rs['surname'],
				'email' => $rs['email'],
				'clientgroupname' => $rs['clientgroup'],
				'clientgroupid' => $rs['clientgroupid']
			);
		}
		else{
			throw new FrontendException($this->registry->core->getMessage('ERR_CLIENT_DATA_NO_EXIST'));
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
			throw new FrontendException($this->registry->core->getMessage('ERR_BILLING_ADDRESS_NO_EXIST'));
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
			throw new FrontendException($this->registry->core->getMessage('ERR_DELIVERY_ADDRESS_NO_EXIST'));
		}
		return $Data;
	}

	public function getProducts ($id)
	{
		$sql = "SELECT 
					P.ean AS ean,
					P.barcode AS barcode,
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
				LEFT JOIN product P ON OP.productid = P.idproduct
				WHERE OP.orderid=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'productid' => $rs['id'],
				'ean' => $rs['ean'],
				'barcode' => $rs['barcode'],
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
					A.idproductattributeset AS variantid,
					A.symbol AS ean,
					A.symbol AS barcode,
					GROUP_CONCAT(SUBSTRING(CONCAT(\' \', CONCAT(AP.name,\': \',C.name)), 1) SEPARATOR \',\') AS name
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
			$Data = Array();
			while ($rs = $stmt->fetch()){
				$Data[] = Array(
					'variantid' => $rs['variantid'],
					'ean' => $rs['ean'],
					'barcode' => $rs['barcode'],
					'name' => $rs['name']
				);
			}
			return $Data;
		}
		else{
			return Array();
		}
	}

	public function getDispatchmethodPrice ($id)
	{
		$sql = 'SELECT 
					iddispatchmethodprice as id, 
					dispatchmethodcost, 
					`from`, 
					`to`, 
					vat 
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
		$sql = 'SELECT 
					cost, 
					`from`, 
					`to`,
					vat
				FROM dispatchmethodweight
				WHERE dispatchmethodid = :id';
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
			$vatData = $this->getVATAll();
			$vatValue = $vatData[$method['vat']];
		}
		else{
			$vatValue = 0;
		}
		return $vatValue;
	}

	public function getVATAll ()
	{
		$sql = 'SELECT 
					V.idvat AS id, 
					V.value,	
					VT.name 
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

	public function getOrderHistory ($id)
	{
		$sql = "SELECT 
					OH.content, 
					OST.name as orderstatusname, 
					OH.inform, 
					OH.adddate as date, 
					UD.firstname, 
					UD.surname
				FROM orderhistory OH
				LEFT JOIN orderstatus OS ON OS.idorderstatus = OH.orderstatusid
				LEFT JOIN orderstatustranslation OST ON OS.idorderstatus = OST.orderstatusid AND OST.languageid = :languageid
				LEFT JOIN userdata UD ON UD.userid = OH.addid
				WHERE OH.orderid=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'content' => $rs['content'],
				'date' => $rs['date'],
				'inform' => $rs['inform'],
				'orderstatusname' => $rs['orderstatusname'],
				'firstname' => $rs['firstname'],
				'surname' => $rs['surname']
			);
		}
		return $Data;
	}
} 