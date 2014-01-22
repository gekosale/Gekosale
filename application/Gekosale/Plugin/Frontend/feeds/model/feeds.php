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
 * $Revision: 583 $
 * $Author: gekosale $
 * $Date: 2011-10-28 22:19:07 +0200 (Pt, 28 paź 2011) $
 * $Id: feeds.php 583 2011-10-28 20:19:07Z gekosale $
 */

namespace Gekosale\Plugin;

class FeedsModel extends Component\Model
{

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
	}

	public function getClientsList ()
	{
		$sql = 'SELECT 
					CD.clientid as id, 
					CD.phone, 
					CD.phone2, 					
					CD.description, 
					CD.email, 
					CD.firstname, 
					CD.surname,	
					CA.nip, 
					CA.street, 
					CA.streetno, 
					CA.placeno, 
					CA.companyname, 
					CG.name AS clientgroup, 
					V.value AS vat
				FROM clientdata CD
				LEFT JOIN client C ON CD.clientid= C.idclient
				LEFT JOIN clientgrouptranslation CG ON CG.clientgroupid = CD.clientgroupid AND CG.languageid= :languageid
				LEFT JOIN clientaddress CA ON CA.clientid = C.idclient
				LEFT JOIN vat V ON V.idvat = CA.vatid
		';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
$rs = $stmt->fetch();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'firstname' => $rs['firstname'],
				'surname' => $rs['surname'],
				'email' => $rs['email'],
				'phone' => $rs['phone'],
				'phone2' => $rs['phone2'],				
				'description' => $rs['description'],
				'nip' => $rs['nip'],
				'street' => $rs['street'],
				'streetno' => $rs['streetno'],
				'placeno' => $rs['placeno'],
				'vat' => $rs['vat'],
				'clientgroup' => $rs['clientgroup'],
				'companyname' => $rs['companyname'],
			);
			return $Data;
		}
		throw new CoreException($this->trans('ERR_CLIENT_NO_EXIST'));
	}

	public function getOrderList ()
	{
		$sql = "SELECT O.idorder as id, O.price, O.adddate as date, O.globalprice, O.dispatchmethodprice,
						O.dispatchmethodname, O.paymentmethodname,
						OST.name as orderstatusname,
						OCDelivery.firstname, OCDelivery.surname, OCDelivery.street, OCDelivery.streetno,
						OCDelivery.companyname, OCDelivery.NIP, OCDelivery.placeno, OCDelivery.postcode,
						OCDelivery.place,
						OCDelivery.phone,
						OCDelivery.phone2,
						OCDelivery.email
					FROM `order` O
					LEFT JOIN orderstatus OS ON OS.idorderstatus=O.orderstatusid
          			LEFT JOIN orderstatustranslation OST ON OST.orderstatusid = OS.idorderstatus
              			AND OST.languageid= :languageid
					LEFT JOIN orderclientdeliverydata OCDelivery ON OCDelivery.orderid= O.idorder";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
$rs = $stmt->fetch();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'price' => $rs['price'],
				'orderdate' => $rs['date'],
				'globalprice' => $rs['globalprice'],
				'dispatchmethodprice' => $rs['dispatchmethodprice'],
				'dispatchmethodname' => $rs['dispatchmethodname'],
				'paymentmethodname' => $rs['paymentmethodname'],
				'orderstatusname' => $rs['orderstatusname'],
				'firstname' => $rs['firstname'],
				'surname' => $rs['surname'],
				'street' => $rs['street'],
				'streetno' => $rs['streetno'],
				'companyname' => $rs['companyname'],
				'NIP' => $rs['nip'],
				'placeno' => $rs['placeno'],
				'postcode' => $rs['postcode'],
				'place' => $rs['place'],
				'phone' => $rs['phone'],
				'phone2' => $rs['phone2'],				
				'email' => $rs['email']
			);
		}
		return $Data;
	}

	public function getProductForOrder ($id)
	{
		$sql = "SELECT O.idorder as id, OP.name as productname, OP.price, OP.qty, OPA.name as attributename
					FROM `order` O
						LEFT JOIN orderproduct OP ON OP.orderid= O.idorder
						LEFT JOIN orderproductattribute OPA ON OPA.orderproductid= OP.idorderproduct
					WHERE O.idorder= :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
$rs = $stmt->fetch();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'productname' => $rs['productname'],
				'attributename' => $rs['attributename'],
				'price' => $rs['price'],
				'qty' => $rs['qty']
			);
		}
		return $Data;
	}
}
?>