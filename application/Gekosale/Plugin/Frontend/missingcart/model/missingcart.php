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
 * $Id: missingcart.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Plugin;

class missingcartModel extends Component\Model
{
	
	// ____________________________________________________________________________________________//
	// //
	// FUNKCJE WSPOMAGAJĄCE GARBAGE COLLECTOR //
	// ____________________________________________________________________________________________//
	
	// zapisywanie danych do tablicy missingCart.
	// dane uzupełniane są, jeśli zadziała garbage collector
	// na wybraną sesję, której kontent zawiera niepusty koszyk
	public function saveMissingCartData ($cart, $sessionid)
	{
		try{
			$missingCartId = $this->saveMissingCart($sessionid);
			if ($missingCartId > 0){
				$this->saveMissingCartProducts($cart, $missingCartId);
			}
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
	}
	
	// zapisanie danych o porzuconym koszyku- tablica missingCart
	public function saveMissingCart ($sessionid)
	{
		$sql = "INSERT INTO missingcart	(clientid, clientmail, sessionid, viewid)
				VALUES (:clientid, :clientmail, :sessionid, :viewid)";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', $_SESSION['CurrentState']['MainsideViewId'][0]);
		$stmt->bindValue('clientid', $_SESSION['CurrentState']['Clientid'][0]);
		$stmt->bindValue('clientmail', $_SESSION['CurrentState']['ClientEmail'][0]);
		$stmt->bindValue('sessionid', $sessionid);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}
	
	// zapis do bazy info o produktach w koszyku
	public function saveMissingCartProducts ($cart, $missingCartId)
	{
		$viewid = $_SESSION['CurrentState']['MainsideViewId'][0];
		foreach ($cart as $productsmissingcart => $product){
			
			if (isset($product['standard']) && $product['standard'] == 1){
				$sql = "SELECT * FROM product WHERE idproduct = :id";
				$Data = Array();
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('id', $product['idproduct']);
				$stmt->execute();
				$rs = $stmt->fetch();
				if ($rs){
					$sql = "INSERT INTO missingcartproduct (missingcartid, productid, stock, qty, productattributesetid, viewid) 
							VALUES (:missingcartid, :productid, :stock, :qty, 0, :viewid)";
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('missingcartid', $missingCartId);
					$stmt->bindValue('productid', $product['idproduct']);
					$stmt->bindValue('stock', $product['stock']);
					$stmt->bindValue('qty', $product['qty']);
					$stmt->bindValue('viewid', $viewid);
					try{
						$stmt->execute();
					}
					catch (Exception $e){
						throw new Exception($e->getMessage());
					}
				}
			}
			// zapis produktów z atrybutami
			if (isset($product['attributes']) && $product['attributes'] != NULL){
				foreach ($product['attributes'] as $attributesmissingcart => $attribute){
					$sql = "SELECT * FROM product WHERE idproduct = :id";
					$Data = Array();
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('id', $attribute['idproduct']);
					$stmt->execute();
					$rs = $stmt->fetch();
					if ($rs){
						$sql = "INSERT INTO missingcartproduct (missingcartid, productid, stock, qty, productattributesetid, viewid)
								VALUES (:missingcartid, :productid, :stock, :qty, :productattributesetid, :viewid)";
						$stmt = Db::getInstance()->prepare($sql);
						$stmt->bindValue('missingcartid', $missingCartId);
						$stmt->bindValue('productid', $attribute['idproduct']);
						$stmt->bindValue('stock', $attribute['stock']);
						$stmt->bindValue('qty', $attribute['qty']);
						$stmt->bindValue('productattributesetid', $attribute['attr']);
						$stmt->bindValue('viewid', $viewid);
						try{
							$stmt->execute();
						}
						catch (Exception $e){
							throw new Exception($e->getMessage());
						}
					}
				}
			}
		}
	}
	
	// Stwórz tablicę z informacjami o usuwanej przez garbage collector sesji
	// Tablica zostanie utwórzona tylko wtedy, gdy dana sesja ($sessionid)
	// będzie ostatnią zapisaną sesją danego klienta ($rs['sessionid'])
	public function checkMissingCartSessionid ($sessionid)
	{
		$sql = "SELECT 
					S.sessionid, 
					S.sessioncontent, 
					S.adddate, 
					S.clientid,
					S.cart
				FROM sessionhandler S 
				WHERE S.sessionid = :sessionid";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('sessionid', $sessionid);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$Data = Array(
					'sessionid' => $sessionid,
					'sessioncontent' => $rs['sessioncontent'],
					'cart' => json_decode($rs['cart'], true),
					'adddate' => $rs['adddate']
				);
			}
		}
		catch (Exception $e){
			new FrontendException($e->getMessage());
		}
		return $Data;
	}
	
	// sprawdź, czy usuwana przez garbage collector sesja, zawiera dane o
	// koszyku
	// i koszyk należy do zalogowanego klienta- jeśli tak, to true,
	// w przeciwnym razie false
	public function checkSessionHandlerHasCartData ($cart)
	{
		if (is_array($cart) && ! empty($cart)){
			return true;
		}
		else{
			return false;
		}
	}
	
	// ____________________________________________________________________________________________//
	// //
	// FUNKCJE OBSŁUGUJĄCE PORZUCONE KOSZYKI //
	// ____________________________________________________________________________________________//
	public function checkMissingCartForClient ($clientid)
	{
		return $this->makeCartFromMissingCart($this->getMissingCart($clientid));
	}
	
	// utworenie tablicy z poprzedniej sesji danego klienta.
	public function getClientMissingCartSessionHandler ($clientid)
	{
		$sql = "SELECT 
					S.sessionid, 
					S.sessioncontent, 
					S.adddate, 
					S.clientid
				FROM sessionhandler S
				WHERE S.clientid = :clientid
	          	ORDER BY S.adddate DESC
  				LIMIT 1";
		
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', $clientid);
		try{
			$Data = Array();
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$Data = Array(
					'sessionid' => $rs['sessionid'],
					'sessioncontent' => $rs['sessioncontent'],
					'decodecontent' => $this->decode_session($rs['sessioncontent']),
					'adddate' => $rs['adddate']
				);
			}
			else{
				$Data = 0;
			}
		}
		catch (Exception $fe){
			new FrontendException($fe->message('Error while selecting session content from sessionhandler.'));
		}
		return $Data;
	}
	
	// pobranie danych z missing cart
	public function getMissingCart ($clientid)
	{
		$sql = "SELECT 
					MC.idmissingcart, 
					MC.sessionid 
				FROM missingcart MC
				WHERE MC.clientid = :clientid
				ORDER BY MC.adddate DESC
				LIMIT 1";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		$Data = Array();
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$Data = Array(
					'clientid' => $clientid,
					'idmissingcart' => $rs['idmissingcart'],
					'products' => $this->getProductFromMissingCart($rs['idmissingcart']),
					'sessionid' => $rs['sessionid']
				);
			}
			else{
				$Data = 0;
			}
		}
		catch (Exception $fe){
			new FrontendException($fe->message('Error while selecting session content from sessionhandler.'));
		}
		return $Data;
	}

	public function cleanMissingCart ($clientId)
	{
		$sql = "DELETE FROM missingcartproduct WHERE missingcartid IN(SELECT idmissingcart FROM missingcart WHERE clientid = :clientid)";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', $clientId);
		$stmt->execute();
		
		$sql = "DELETE FROM missingcart WHERE clientid = :clientid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', $clientId);
		$stmt->execute();
	}
	
	// missing cart-pobranie informacji o produktach
	public function getProductFromMissingCart ($idmissingcart)
	{
		$sql = "SELECT 
					MCP.idmissingcartproduct, 
					MCP.productid, 
					MCP.qty, 
					MCP.productattributesetid
				FROM missingcartproduct MCP
				WHERE missingcartid = :idmissingcart";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idmissingcart', $idmissingcart);
		$Data = Array();
		try{
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[] = Array(
					'idmissingcartproduct' => $rs['idmissingcartproduct'],
					'productid' => $rs['productid'],
					'qty' => $rs['qty'],
					'productattributesetid' => $rs['productattributesetid']
				);
			}
		}
		catch (Exception $fe){
			new FrontendException($fe->message('Error while selecting product from missingcart.'));
		}
		return $Data;
	}

	public function makeCartFromSessionhandler ($DataSessionHandler)
	{
		$Data = Array();
		if (is_array($DataSessionHandler) && isset($DataSessionHandler['decodecontent']['CurrentState']['Cart'])){
			foreach ($DataSessionHandler['decodecontent']['CurrentState']['Cart'][0] as $products){
				if (isset($products['attributes']) && $products['attributes'] != NULL){
					foreach ($products['attributes'] as $attr){
						$Data[$products['idproduct']] = Array(
							'idproduct' => $products['idproduct'],
							'attributes' => $attr['attr'],
							'qty' => $attr['qty']
						);
					}
				}
				if (isset($products['standard']) && $products['standard'] == 1){
					$Data[$products['idproduct']] = Array(
						'idproduct' => $products['idproduct'],
						'qty' => $products['qty'],
						'standard' => $products['standard'],
						'attributes' => $products['attributes']
					);
				}
			}
		}
		else{
			$Data = 0;
		}
		return $Data;
	}

	public function makeCartFromMissingCart ($DataMissingCart)
	{
		$Data = Array();
		if (is_array($DataMissingCart['products'])){
			foreach ($DataMissingCart['products'] as $key => $products){
				if ($products['productattributesetid'] != NULL && $products['productattributesetid'] > 0){
					$Data[$products['productid']]['attributes'][$products['productattributesetid']] = Array(
						'idproduct' => $products['productid'],
						'attributes' => $products['productattributesetid'],
						'qty' => $products['qty']
					);
				}
				else{
					$Data[$products['productid']] = Array(
						'idproduct' => $products['productid'],
						'qty' => $products['qty'],
						'standard' => 1
					);
				}
			}
		}
		else{
			$Data = 0;
		}
		return $Data;
	}

	public function decode_session ($sessioncontent)
	{
		$serializecontent = $sessioncontent;
		$unserializecontent = $this->unserializesession($serializecontent);
		return $unserializecontent;
	}

	function unserializesession ($serializecontent)
	{
		$result = Array();
		$vars = preg_split('/([a-zA-Z0-9]+)\|/', $serializecontent, - 1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		$count = count($vars);
		for ($i = 0; $i < $count; $i ++){
			$result[$vars[$i ++]] = unserialize($vars[$i]);
		}
		return $result;
	}
}