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
 * $Id: mainside.php 583 2011-10-28 20:19:07Z gekosale $ 
 */
namespace Gekosale\Plugin;

class MainsideModel extends Component\Model
{

	public function salesChart ($request)
	{
		$from = $request['from'];
		$to = $request['to'];
		$sql = 'SELECT 
					DATE(adddate) AS adddate,
					ROUND(SUM(globalprice),2) as total
				FROM `order`
				WHERE (DATE(adddate) BETWEEN DATE(:from) AND DATE(:to)) AND viewid IN (' . Helper::getViewIdsAsString() . ')
				GROUP BY DATE(adddate) 
				ORDER BY adddate ASC';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('from', $from);
		$stmt->bindValue('to', $to);
		$stmt->execute();
		$Data = Array();
		
		$Data['cols'][] = Array(
			'id' => '',
			'label' => 'Data',
			'pattern' => '',
			'type' => 'string'
		);
		
		$Data['cols'][] = Array(
			'id' => '',
			'label' => 'Suma',
			'pattern' => '',
			'type' => 'number'
		);
		
		while ($rs = $stmt->fetch()){
			$Data['rows'][]['c'] = Array(
				Array(
					'v' => $rs['adddate']
				),
				Array(
					'v' => round($rs['total'], 0)
				)
			);
		}
		return json_encode($Data);
	}

	public function ordersChart ($request)
	{
		$seriesXML = '';
		$graphsXML = '';
		$from = $request['from'];
		$to = $request['to'];
		$sql = 'SELECT 
					DATE(adddate) AS adddate,
					COUNT(idorder) as total
				FROM `order`
				WHERE (DATE(adddate) BETWEEN DATE(:from) AND DATE(:to)) AND viewid IN (' . Helper::getViewIdsAsString() . ')
				GROUP BY DATE(adddate) 
				ORDER BY adddate ASC';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('from', $from);
		$stmt->bindValue('to', $to);
		$stmt->execute();
		$Data = Array();
		
		$Data['cols'][] = Array(
			'id' => '',
			'label' => 'Data',
			'pattern' => '',
			'type' => 'string'
		);
		
		$Data['cols'][] = Array(
			'id' => '',
			'label' => 'Suma',
			'pattern' => '',
			'type' => 'number'
		);
		
		while ($rs = $stmt->fetch()){
			$Data['rows'][]['c'] = Array(
				Array(
					'v' => $rs['adddate']
				),
				Array(
					'v' => round($rs['total'], 0)
				)
			);
		}
		return json_encode($Data);
	}

	public function clientsChart ($request)
	{
		$seriesXML = '';
		$graphsXML = '';
		$from = $request['from'];
		$to = $request['to'];
		$sql = 'SELECT 
					DATE(adddate) AS adddate,
					COUNT(idclient) as total
				FROM `client`
				WHERE (DATE(adddate) BETWEEN DATE(:from) AND DATE(:to)) AND viewid IN (' . Helper::getViewIdsAsString() . ')
				GROUP BY DATE(adddate) 
				ORDER BY adddate ASC';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('from', $from);
		$stmt->bindValue('to', $to);
		$stmt->execute();
		$Data = Array();
		
		$Data['cols'][] = Array(
			'id' => '',
			'label' => 'Data',
			'pattern' => '',
			'type' => 'string'
		);
		
		$Data['cols'][] = Array(
			'id' => '',
			'label' => 'Suma',
			'pattern' => '',
			'type' => 'number'
		);
		
		while ($rs = $stmt->fetch()){
			$Data['rows'][]['c'] = Array(
				Array(
					'v' => $rs['adddate']
				),
				Array(
					'v' => round($rs['total'], 0)
				)
			);
		}
		return json_encode($Data);
	}

	public function productsChart ($request)
	{
		$seriesXML = '';
		$graphsXML = '';
		$from = $request['from'];
		$to = $request['to'];
		$sql = 'SELECT
					OP.name,
					SUM(qty) as total
				FROM `orderproduct` OP
				LEFT JOIN `order` O ON OP.orderid = O.idorder
				WHERE (DATE(O.adddate) BETWEEN DATE(:from) AND DATE(:to)) AND O.viewid IN (' . Helper::getViewIdsAsString() . ')
				GROUP BY OP.name
				LIMIT 100';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('from', $from);
		$stmt->bindValue('to', $to);
		$stmt->execute();
		$Data = Array();
		
		$Data['cols'][] = Array(
			'id' => '',
			'label' => 'Produkt',
			'pattern' => '',
			'type' => 'string'
		);
		
		$Data['cols'][] = Array(
			'id' => '',
			'label' => 'Suma',
			'pattern' => '',
			'type' => 'number'
		);
		
		while ($rs = $stmt->fetch()){
			$Data['rows'][]['c'] = Array(
				Array(
					'v' => $rs['name']
				),
				Array(
					'v' => round($rs['total'], 0)
				)
			);
		}
		
		if (! isset($Data['rows'])){
			$Data['rows'][]['c'] = Array(
				Array(
					'v' => 'Brak danych o sprzedaży'
				),
				Array(
					'v' => 1
				)
			);
		}
		return json_encode($Data);
	}

	public function getLastOrder ()
	{
		$sql = 'SELECT AES_DECRYPT(OCD.surname, :encryptionKey) surname, O.price, OCD.`adddate`, O.idorder 
						FROM `order` O
						LEFT JOIN orderclientdata OCD ON OCD.orderid=idorder
						WHERE O.viewid IN (' . Helper::getViewIdsAsString() . ')
 						ORDER BY idorder DESC LIMIT 10';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'price' => sprintf('%.2f', $rs['price']),
				'id' => $rs['idorder'],
				'surname' => $rs['surname']
			);
		}
		return $Data;
	}

	public function getNewClient ()
	{
		$sql = 'SELECT 
					CD.clientid,
					AES_DECRYPT(surname, :encryptionKey) surname, 
					AES_DECRYPT(firstname, :encryptionKey) firstname 
				FROM clientdata CD
				LEFT JOIN client C ON C.idclient=clientid
				WHERE C.viewid IN (' . Helper::getViewIdsAsString() . ')
 				ORDER BY CD.adddate DESC LIMIT 10';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'surname' => $rs['surname'],
				'id' => $rs['clientid'],
				'firstname' => $rs['firstname']
			);
		}
		return $Data;
	}

	public function getTopTen ()
	{
		$sql = 'SELECT 
					OP.productid, 
					ROUND(OP.qty * OP.price,2) as productprice, 
					OP.name as productname, 
					SUM(OP.qty) as bestorder
				FROM orderproduct OP
				LEFT JOIN `order` O ON O.idorder = OP.orderid
				LEFT JOIN product P ON P.idproduct = OP.productid
				WHERE O.viewid IN (' . Helper::getViewIdsAsString() . ')
 				GROUP BY OP.productid 
 				ORDER BY bestorder DESC LIMIT 10';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'productprice' => sprintf('%.2f', $rs['productprice']),
				'label' => $rs['productname'],
				'value' => $rs['bestorder'],
				'productid' => $rs['productid']
			);
		}
		return $Data;
	}

	public function getOpinions ()
	{
		$sql = 'SELECT idproductreview, nick, review FROM productreview WHERE enable != 1 AND viewid = :viewid ORDER BY adddate DESC limit 10';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function getMostSearch ()
	{
		$sql = 'SELECT 
					textcount as qty, 
					name as productname 
				FROM mostsearch 
				WHERE viewid IN (' . Helper::getViewIdsAsString() . ')
				GROUP BY name ORDER BY qty DESC LIMIT 10';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'productname' => $rs['productname'],
				'qty' => $rs['qty']
			);
		}
		return $Data;
	}

	public function getStock ()
	{
		$sql = "SELECT 
					PT.name, 
					P.stock
				FROM product P
				LEFT JOIN producttranslation PT ON PT.productid = P.idproduct AND languageid=:languageid
				WHERE P.stock < 10 ORDER BY stock";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'label' => $rs['name'],
				'value' => $rs['stock']
			);
		}
		return $Data;
	}

	public function getClientOnline ()
	{
		$sql = 'SELECT 
					SH.sessionid, 
					SH.clientid, 
					AES_DECRYPT(CD.firstname, :encryptionKey) firstname, 
					AES_DECRYPT(CD.surname, :encryptionKey) surname,
					CONCAT(SH.globalprice,\' \',SH.cartcurrency) AS cart
				FROM sessionhandler  SH
				LEFT JOIN clientdata CD ON CD.clientid = SH.clientid
				WHERE SH.viewid IN (' . Helper::getViewIdsAsString() . ')
				GROUP BY SH.sessionid 
				ORDER BY SH.expiredate DESC
				LIMIT 10';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$Data = Array();
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'client' => ($rs['clientid'] > 0) ? $rs['firstname'] . ' ' . $rs['surname'] : $this->trans('TXT_GUEST'),
				'clientid' => $rs['clientid'],
				'cart' => $rs['cart']
			);
		}
		return $Data;
	}

	public function getSummaryStats ()
	{
		$Data = Array();
		$period = date("Ym");
		$sql = 'SELECT ROUND(SUM(globalprice),2) as total, COUNT(idorder) as orders
					FROM `order`
					WHERE viewid IN (' . Helper::getViewIdsAsString() . ') AND DATE_FORMAT(adddate,\'%Y%m\') = :period';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('period', $period);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data['summarysales'] = Array(
				'total' => sprintf('%.2f', $rs['total']),
				'orders' => $rs['orders']
			);
		}
		// Daily sales
		$sql = 'SELECT ROUND(SUM(globalprice),2) as total, COUNT(idorder) as orders
					FROM `order`
					WHERE viewid IN (' . Helper::getViewIdsAsString() . ') AND DATE_FORMAT(adddate,\'%Y-%m-%d\') = CURDATE()';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data['todaysales'] = Array(
				'total' => sprintf('%.2f', $rs['total']),
				'orders' => $rs['orders']
			);
		}
		// Total clients
		$sql = 'SELECT COUNT(idclient) as totalclients
					FROM `client`
					WHERE viewid IN (' . Helper::getViewIdsAsString() . ') AND DATE_FORMAT(adddate,\'%Y%m\') = :period';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('period', $period);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data['summaryclients'] = Array(
				'totalclients' => (int) $rs['totalclients']
			);
		}
		// Daily clients
		$sql = 'SELECT COUNT(idclient) as clients
					FROM `client`
					WHERE viewid IN (' . Helper::getViewIdsAsString() . ') AND DATE_FORMAT(adddate,\'%Y-%m-%d\') = CURDATE()';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data['todayclients'] = Array(
				'totalclients' => (int) $rs['clients']
			);
		}
		return $Data;
	}

	public function getMostViewedProducts ($url, $productData)
	{
		$Data = Array();
		foreach ($productData as $key => $productid){
			$sql = 'SELECT name	FROM producttranslation WHERE productid=:productid AND languageid=:languageid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('languageid', Helper::getLanguageId());
			$stmt->bindValue('productid', $productid);
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[] = Array(
					'name' => $rs['name'],
					'id' => $productid,
					'qty' => $url[$key]['qty']
				);
			}
		}
		return $Data;
	}

	public function search ($phrase)
	{
		$phrase = strtolower($phrase);
		
		$sql = '
			SELECT 
				O.idorder, 
				O.adddate,
				AES_DECRYPT(OC.surname,:encryptionkey) AS surname,
				AES_DECRYPT(OC.firstname,:encryptionkey) AS firstname,
				AES_DECRYPT(OC.email,:encryptionkey) AS email
			FROM `order` O
			LEFT JOIN orderclientdata OC ON OC.orderid=O.idorder
			WHERE 
				O.idorder = :id OR
				CONVERT(LOWER(AES_DECRYPT(OC.surname,:encryptionkey)) USING utf8) LIKE :phrase OR
				CONVERT(LOWER(AES_DECRYPT(OC.firstname,:encryptionkey)) USING utf8) LIKE :phrase OR
				CONVERT(LOWER(AES_DECRYPT(OC.email,:encryptionkey)) USING utf8) LIKE :phrase
			ORDER BY O.adddate DESC
			LIMIT 10
			';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $phrase);
		$stmt->bindValue('phrase', '%' . $phrase . '%');
		$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$url = App::getURLAdressWithAdminPane() . 'order/edit/' . $rs['idorder'];
			$str = '#' . $rs['idorder'] . ': ' . $rs['firstname'] . ' ' . $rs['surname'] . ' (' . $rs['email'] . ') z dnia ' . $rs['adddate'];
			$str = $this->highlight($phrase, $str);
			$str = '<li><a href="' . $url . '">' . $str . '</a></li>';
			$Data['orders'][] = $str;
		}
		
		$sql = '
			SELECT 
				OC.clientid, 
				AES_DECRYPT(OC.surname,:encryptionkey) AS surname,
				AES_DECRYPT(OC.firstname,:encryptionkey) AS firstname,
				AES_DECRYPT(OC.email,:encryptionkey) AS email
			FROM clientdata OC
			WHERE 
				CONVERT(LOWER(AES_DECRYPT(OC.surname,:encryptionkey)) USING utf8) LIKE :phrase OR
				CONVERT(LOWER(AES_DECRYPT(OC.firstname,:encryptionkey)) USING utf8) LIKE :phrase OR
				CONVERT(LOWER(AES_DECRYPT(OC.email,:encryptionkey)) USING utf8) LIKE :phrase
			LIMIT 10
			';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $phrase);
		$stmt->bindValue('phrase', '%' . $phrase . '%');
		$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$url = App::getURLAdressWithAdminPane() . 'client/edit/' . $rs['clientid'];
			$str = $rs['firstname'] . ' ' . $rs['surname'] . ' (' . $rs['email'] . ')';
			$str = $this->highlight($phrase, $str);
			$str = '<li><a href="' . $url . '">' . $str . '</a></li>';
			$Data['clients'][] = $str;
		}
		
		$sql = '
			SELECT 
				PT.productid,
				PT.name,
				P.ean,
				P.delivelercode
			FROM product P
			LEFT JOIN producttranslation PT ON PT.productid = P.idproduct AND PT.languageid = :languageid
			WHERE 
				PT.name LIKE :phrase OR
				P.ean LIKE :phrase OR
				P.delivelercode LIKE :phrase
			LIMIT 20
			';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $phrase);
		$stmt->bindValue('phrase', '%' . $phrase . '%');
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$url = App::getURLAdressWithAdminPane() . 'product/edit/' . $rs['productid'];
			$str = $rs['name'];
			if ($rs['ean'] != ''){
				$str .= ', EAN: ' . $rs['ean'];
			}
			$str = $this->highlight($phrase, $str);
			$str = '<li><a href="' . $url . '">' . $str . '</a></li>';
			$Data['products'][] = $str;
		}
		
		return $Data;
	}

	public function highlight ($needle, $haystack)
	{
		$ind = stripos($haystack, $needle);
		$len = strlen($needle);
		if ($ind !== false){
			return substr($haystack, 0, $ind) . "<b>" . substr($haystack, $ind, $len) . "</b>" . $this->highlight($needle, substr($haystack, $ind + $len));
		}
		else
			return $haystack;
	}
}