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
use sfEvent;

class MissingCartModel extends Component\Model
{

	public function checkMissingCartForClient ($clientid)
	{
		$checkDataFromMissingCart = $this->getMissingCart($clientid);
		$Data = Array();
		if (! is_array($checkDataFromMissingCart) && ($checkDataFromMissingCart == 0 || $checkDataFromMissingCart == NULL)){
			$checkDataSessionHandler = $this->getClientMissingCartSessionHandler($clientid);
			if ($checkDataSessionHandler != 0 || $checkDataSessionHandler != NULL){
				$DataSessionHandler = $this->makeCartFromSessionhandler($checkDataSessionHandler);
				$Data = $DataSessionHandler;
			}
		}
		else{
			$DataMissingCart = $this->makeCartFromMissingCart($checkDataFromMissingCart);
			$Data = $DataMissingCart;
		}
		return $Data;
		;
	}

	public function getClientMissingCartSessionHandler ($clientid)
	{
		$sql = "SELECT S.sessionid, S.sessioncontent, S.adddate, S.clientid
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
		catch (Exception $e){
			new Exception($e->message('Error while selecting session content from sessionhandler.'));
		}
		return $Data;
	}

	public function getMissingCart ($clientid)
	{
		$sql = "SELECT 
					MC.idmissingcart, 
					MC.dispatchmethodid, 
					MC.paymentmethodid 
				FROM missingcart MC
				WHERE MC.clientid = :clientid";
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
					'dispatchmethodid' => $rs['dispatchmethodid'],
					'paymentmethodid' => $rs['paymentmethodid'],
					'products' => $this->getProductFromMissingCart($idmissingcart),
					'sessionid' => $this['sessionid']
				);
			}
			else{
				$Data = 0;
			}
		}
		catch (Exception $e){
			new Exception($e->message('Error while selecting session content from sessionhandler.'));
		}
		return $Data;
	}

	public function getProductFromMissingCart ($idmissingcart)
	{
		$sql = "SELECT 
					MCP.idmissingcartproduct, 
					MCP.productid, 
					MCP.qty, 
					MCP.productattributesetid
				FROM missingcartproduct MCP
				LEFT JOIN product P ON P.idproduct = MCP.productid
				WHERE missingcartid = :idmissingcart";
		$stmt->this->registry->db->prepareStatement($sql);
		$stmt->bindValue('idmissingcart', $idmissingcart);
		$Data = Array();
		try{
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[] = Array(
					'idmissingcartproduct' => $rs['idmissingcartproduct'],
					'productid' => $rs['productid'],
					'qty' => $rs['qty'],
					'productattributesetid' => $rs['productattributesetid'],
					'name' => $rs['name']
				);
			}
		}
		catch (Exception $e){
			new Exception($e->message('Error while selecting product from missingcart.'));
		}
	}

	public function makeCartFromSessionhandler ($DataSessionHandler)
	{
		$Data = Array();
		if (is_array($DataSessionHandler) && isset($DataSessionHandler['decodecontent']['CurrentState']['Cart'])){
			foreach ($DataSessionHandler['decodecontent']['CurrentState']['Cart'][0] as $products){
				if (isset($products['attributes']) && $products['attributes'] != NULL){
					foreach ($products['attributes'] as $attr){
						$Data[] = Array(
							'id' => $products['idproduct'],
							'attributes' => $attr['attr'],
							'qty' => $attr['qty'],
							'name' => $attr['name']
						);
					}
				}
				if (isset($products['standard']) && $products['standard'] == 1){
					$Data[] = Array(
						'id' => $products['idproduct'],
						'qty' => $products['qty'],
						'standard' => $products['standard'],
						'attributes' => $products['attributes'],
						'name' => $products['name']
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
		if (is_array($DataMissingCart)){
			foreach ($DataMissingCart['products'] as $products){
				if ($products['productattributesetid'] != NULL){
					$Data[$products['idproduct']]['attributes'][$products['productattributesetid']] = Array(
						'id' => $products['idproduct'],
						'attributes' => $products['productattributesetid'],
						'qty' => $attr['qty'],
						'name' => $attr['name']
					);
				}
				else{
					$Data[$products['idproduct']] = Array(
						'id' => $products['productid'],
						'qty' => $products['qty'],
						'standard' => 1,
						'name' => $products['name']
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
		for ($i = 0; $i <= 1; $i ++){
			$result[$vars[$i ++]] = unserialize($vars[$i]);
		}
		return $result;
	}
}