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
 * $Id: rulescart.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale\Plugin;

class RulesCartModel extends Component\Model
{

	public function doAJAXDeleteRulesCart ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteRulesCart'
		), $this->getName());
	}

	public function deleteRulesCart ($id)
	{
		DbTracker::deleteRows('rulescart', 'idrulescart', $id);
	}

	public function getRulesCartAll ()
	{
		$sql = 'SELECT 
					RC.idrulescart AS id,
					RCT.name,
					RC.distinction
				FROM rulescart RC
				LEFT JOIN rulescarttranslation RCT ON RCT.rulescartid = RC.idrulescart AND RCT.languageid = :languageid
				ORDER BY distinction';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $rs['name'],
				'distinction' => $rs['distinction'],
				'parent' => null
			);
		}
		return $Data;
	}

	public function getProductsRulesCart ($id)
	{
		$sql = "SELECT 
					P.name as productname
				FROM product P
				LEFT JOIN productcategory PC ON PC.productid = P.idproduct
				WHERE categoryid=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'productname' => $rs['productname']
			);
		}
		return $Data;
	}

	public function getRulesCartView ($id)
	{
		$sql = 'SELECT 
					idrulescart AS id, 
					suffixtypeid,
		 			discount, 
					datefrom, 
					dateto, 
					discountforall,
					freeshipping
				FROM rulescart
				WHERE idrulescart= :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'id' => $rs['id'],
				'language' => $this->getRulesCartTranslation($rs['id']),
				'discount' => $rs['discount'],
				'suffixtypeid' => $rs['suffixtypeid'],
				'datefrom' => $rs['datefrom'],
				'dateto' => $rs['dateto'],
				'discountforall' => $rs['discountforall'],
				'freeshipping' => $rs['freeshipping']
			);
			return $Data;
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function getRulesCartTranslation ($id)
	{
		$sql = "SELECT 
					name,
					description,
					languageid
				FROM rulescarttranslation
				WHERE rulescartid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['languageid']] = Array(
				'name' => $rs['name'],
				'description' => $rs['description']
			);
		}
		return $Data;
	}

	public function getRulesCartClientGroupView ($id)
	{
		$sql = 'SELECT 
					clientgroupid, 
					suffixtypeid, 
					discount,
					freeshipping
				FROM rulescartclientgroup
				WHERE rulescartid= :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'clientgroupid' => $rs['clientgroupid'],
				'suffixtypeid' => $rs['suffixtypeid'],
				'discount' => $rs['discount'],
				'freeshipping' => $rs['freeshipping']
			);
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return $Data;
	}

	public function getRulesCartDeliverersView ($id)
	{
		$sql = 'SELECT 
					pkid
				FROM rulescartrule
				WHERE rulescartid= :id AND ruleid = (SELECT idrule FROM rule WHERE tablereferer LIKE "dispatchmethod" AND rulekindofid = 2)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['pkid']] = $rs['pkid'];
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return $Data;
	}

	public function getRulesCartPaymentsView ($id)
	{
		$sql = 'SELECT 
					pkid
				FROM rulescartrule
				WHERE rulescartid = :id	AND ruleid = (SELECT idrule FROM rule WHERE tablereferer LIKE "paymentmethod" AND rulekindofid = 2)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['pkid']] = $rs['pkid'];
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return $Data;
	}

	public function getRulesCartOtherDinamicDataConditionsView ($id)
	{
		$sql = 'SELECT 
					RCR.idrulescartrule, 
					RCR.pricefrom, 
					RCR.priceto, 
					RCR.ruleid,
					R.field
				FROM rulescartrule RCR
				LEFT JOIN rule R ON RCR.ruleid = R.idrule
				WHERE rulescartid = :id AND pkid IS NULL';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['idrulescartrule']] = Array(
				'pricefrom' => $rs['pricefrom'],
				'priceto' => $rs['priceto'],
				'ruleid' => $rs['ruleid'],
				'field' => $rs['field']
			);
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return $Data;
	}

	public function getRulesCartViews ($id)
	{
		$sql = "SELECT 
					viewid
				FROM rulescartview
				WHERE rulescartid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['viewid'];
		}
		return $Data;
	}

	public function addEmptyRulesCart ($request)
	{
		$data = Array(
			'name' => (isset($request['name']) && strlen($request['name'])) ? $request['name'] : $this->trans('TXT_NEW_RULES_CART')
		);
		return Array(
			'id' => $this->addRulesCart($data)
		);
	}

	public function changeRulesCartOrder ($request)
	{
		if (! isset($request['items']) || ! is_array($request['items'])){
			throw new Exception('No data received.');
		}
		$sql = 'UPDATE rulescart SET 
					distinction = :distinction
				WHERE idrulescart = :id';
		foreach ($request['items'] as $item){
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $item['id']);
			$stmt->bindValue('distinction', $item['weight']);
			$stmt->execute();
		}
		return Array(
			'status' => $this->trans('TXT_RULE_CART_ORDER_SAVED')
		);
	}

	public function editRulesCart ($submitedData, $idRulesCart)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->rulesCartEdit($submitedData, $idRulesCart);
			$this->rulesCartClientGroupEdit($submitedData, $idRulesCart);
			$this->rulesCartRuleEdit($submitedData, $idRulesCart);
			$this->rulesCartViewEdit($submitedData['view'], $idRulesCart);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_RULE_CART_EDIT'), 3002, $e->getMessage());
		}
		
		Db::getInstance()->commit();
	}

	public function rulesCartEdit ($submitedData, $idRulesCart)
	{
		$sql = 'UPDATE rulescart SET 
					datefrom = :datefrom, 
					dateto = :dateto, 
					discountforall = :discountforall,
					suffixtypeid = :suffixtypeid, 
					discount = :discount,
					freeshipping = :freeshipping
				WHERE idrulescart = :id';
		$stmt = Db::getInstance()->prepare($sql);
		if (isset($submitedData['discountforall']) && $submitedData['discountforall'] == 1){
			$stmt->bindValue('discountforall', $submitedData['discountforall']);
			$stmt->bindValue('suffixtypeid', $submitedData['suffixtypeid']);
			$stmt->bindValue('discount', $submitedData['discount']);
			$stmt->bindValue('freeshipping', $submitedData['freeshipping']);
		}
		else{
			$stmt->bindValue('discountforall', 0);
			$stmt->bindValue('suffixtypeid', NULL);
			$stmt->bindValue('discount', 0);
			$stmt->bindValue('freeshipping', 0);
		}
		$stmt->bindValue('id', $idRulesCart);
		if (isset($submitedData['datefrom']) && ! empty($submitedData['datefrom'])){
			$stmt->bindValue('datefrom', $submitedData['datefrom']);
		}
		else{
			$stmt->bindValue('datefrom', NULL);
		}
		if (isset($submitedData['dateto']) && ! empty($submitedData['dateto'])){
			$stmt->bindValue('dateto', $submitedData['dateto']);
		}
		else{
			$stmt->bindValue('dateto', NULL);
		}
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CATEGORY_UPDATE'), 1, $e->getMessage());
			return false;
		}
		
		DbTracker::deleteRows('rulescarttranslation', 'rulescartid', $idRulesCart);
		
		foreach ($submitedData['name'] as $key => $val){
			$sql = 'INSERT INTO rulescarttranslation SET
						rulescartid = :rulescartid,
						name = :name,
						description = :description,
						languageid = :languageid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('rulescartid', $idRulesCart);
			$stmt->bindValue('name', $submitedData['name'][$key]);
			$stmt->bindValue('description', $submitedData['description'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_RANGETYPE_TRANSLATION_EDIT'), 4, $e->getMessage());
			}
		}
		
		return true;
	}

	public function rulesCartClientGroupEdit ($submitedData, $id)
	{
		DbTracker::deleteRows('rulescartclientgroup', 'rulescartid', $id);
		
		try{
			$clientGroups = App::getModel('clientgroup/clientgroup')->getClientGroupAll();
			if (isset($submitedData['discountforall']) && $submitedData['discountforall'] == 1){
				foreach ($clientGroups as $clientGroup){
					$sql = 'INSERT INTO rulescartclientgroup (rulescartid, clientgroupid, suffixtypeid, discount, freeshipping)
							VALUES (:rulescartid, :clientgroupid, :suffixtypeid, :discount, :freeshipping)';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('rulescartid', $id);
					$stmt->bindValue('clientgroupid', $clientGroup['id']);
					$stmt->bindValue('suffixtypeid', $submitedData['suffixtypeid']);
					$stmt->bindValue('discount', $submitedData['discount']);
					$stmt->bindValue('freeshipping', $submitedData['freeshipping']);
					
					try{
						$stmt->execute();
					}
					catch (Exception $e){
						throw new CoreException($this->trans('ERR_UPDATE_CLIENTGROUP_RULE_CART'), 112, $e->getMessage());
					}
				}
			}
			else{
				foreach ($clientGroups as $clientGroup){
					if (isset($submitedData['groupid_' . $clientGroup['id']]) && $submitedData['groupid_' . $clientGroup['id']] > 0){
						$sql = 'INSERT INTO rulescartclientgroup (rulescartid, clientgroupid, suffixtypeid, discount, freeshipping)
								VALUES (:rulescartid, :clientgroupid, :suffixtypeid, :discount, :freeshipping)';
						$stmt = Db::getInstance()->prepare($sql);
						$stmt->bindValue('rulescartid', $id);
						$stmt->bindValue('clientgroupid', $clientGroup['id']);
						$stmt->bindValue('suffixtypeid', $submitedData['suffixtypeid_' . $clientGroup['id']]);
						$stmt->bindValue('discount', $submitedData['discount_' . $clientGroup['id']]);
						$stmt->bindValue('freeshipping', $submitedData['freeshipping_' . $clientGroup['id']]);
						try{
							$stmt->execute();
						}
						catch (Exception $e){
							throw new CoreException($this->trans('ERR_UPDATE_CLIENTGROUP_RULE'), 112, $e->getMessage());
						}
					}
				}
			}
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_UPDATE_CLIENTGROUP_RULE_CART'), 112, $e->getMessage());
		}
	}

	public function rulesCartRuleEdit ($submitedData, $id)
	{
		DbTracker::deleteRows('rulescartrule', 'rulescartid', $id);
		
		if (isset($submitedData['deliverers']) && $submitedData['deliverers'] != NULL && count($submitedData['deliverers']) > 0){
			foreach ($submitedData['deliverers'] as $delivererKey => $delivererValue){
				$sql = "INSERT INTO rulescartrule (rulescartid, ruleid, pkid)
						VALUES (:rulescartid, :ruleid, :pkid)";
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('rulescartid', $id);
				$stmt->bindValue('ruleid', 9);
				$stmt->bindValue('pkid', $delivererValue);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}
		
		if (isset($submitedData['payments']) && $submitedData['payments'] != NULL && count($submitedData['payments']) > 0){
			foreach ($submitedData['payments'] as $paymentKey => $paymentValue){
				$sql = "INSERT INTO rulescartrule (rulescartid, ruleid, pkid)
						VALUES (:rulescartid, :ruleid, :pkid)";
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('rulescartid', $id);
				$stmt->bindValue('ruleid', 10);
				$stmt->bindValue('pkid', $paymentValue);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}
		
		if (isset($submitedData['cart_price_from']) && $submitedData['cart_price_from'] > 0){
			$sql = "INSERT INTO rulescartrule (rulescartid, ruleid, pricefrom)
					VALUES (:rulescartid, :ruleid, :pricefrom)";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('rulescartid', $id);
			$stmt->bindValue('ruleid', 11);
			$stmt->bindValue('pricefrom', $submitedData['cart_price_from']);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
		
		if (isset($submitedData['cart_price_to']) && $submitedData['cart_price_to'] > 0){
			$sql = "INSERT INTO rulescartrule (rulescartid, ruleid, priceto)
					VALUES (:rulescartid, :ruleid, :priceto)";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('rulescartid', $id);
			$stmt->bindValue('ruleid', 12);
			$stmt->bindValue('priceto', $submitedData['cart_price_to']);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
		
		if (isset($submitedData['dispatch_price_from']) && $submitedData['dispatch_price_from'] > 0){
			$sql = "INSERT INTO rulescartrule (rulescartid, ruleid, pricefrom)
					VALUES (:rulescartid, :ruleid, :pricefrom)";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('rulescartid', $id);
			$stmt->bindValue('ruleid', 13);
			$stmt->bindValue('pricefrom', $submitedData['dispatch_price_from']);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
		
		if (isset($submitedData['dispatch_price_to']) && $submitedData['dispatch_price_to'] > 0){
			$sql = "INSERT INTO rulescartrule (rulescartid, ruleid, priceto)
					VALUES (:rulescartid, :ruleid, :priceto)";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('rulescartid', $id);
			$stmt->bindValue('ruleid', 14);
			$stmt->bindValue('priceto', $submitedData['dispatch_price_to']);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
		
		return true;
	}

	public function rulesCartViewEdit ($views, $id)
	{
		DbTracker::deleteRows('rulescartview', 'rulescartid', $id);
		
		if ($views != NULL && count($views) > 0){
			foreach ($views as $viewKey => $viewValue){
				$sql = "INSERT INTO rulescartview (rulescartid, viewid)
						VALUES (:rulescartid, :viewid)";
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('rulescartid', $id);
				$stmt->bindValue('viewid', $viewValue);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}
	}

	public function addRulesCart ($Data)
	{
		$sql = 'INSERT INTO rulescart (adddate) VALUES (NOW())';
		$stmt = Db::getInstance()->prepare($sql);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CART_RULE_ADD'), 3003, $e->getMessage());
		}
		
		$cartRuleId = Db::getInstance()->lastInsertId();
		
		$sql = 'INSERT INTO rulescarttranslation SET
					rulescartid = :rulescartid,
					name = :name,
					languageid = :languageid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('rulescartid', $cartRuleId);
		$stmt->bindValue('name', $Data['name']);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_RANGETYPE_TRANSLATION_EDIT'), 4, $e->getMessage());
		}
		
		$views = Helper::getViewIds();
		
		foreach ($views as $key => $val){
			if ($val > 0){
				$sql = 'INSERT INTO rulescartview (rulescartid,viewid)
				VALUES (:rulescartid, :viewid)';
				$stmt = Db::getInstance()->prepare($sql);
				
				$stmt->bindValue('rulescartid', $cartRuleId);
				$stmt->bindValue('viewid', $val);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_CATEGORY_VIEW_ADD'), 4, $e->getMessage());
				}
			}
		}
		
		return $cartRuleId;
	}
}