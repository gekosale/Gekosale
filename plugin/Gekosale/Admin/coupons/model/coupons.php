<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace Gekosale;

class CouponsModel extends Component\Model\Datagrid
{

	public function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('coupons', Array(
			'id' => Array(
				'source' => 'C.idcoupons'
			),
			'name' => Array(
				'source' => 'CT.name'
			),
			'datefrom' => Array(
				'source' => 'C.datefrom'
			),
			'dateto' => Array(
				'source' => 'C.dateto'
			),
			'globalqty' => Array(
				'source' => 'C.globalqty'
			),
			'used' => Array(
				'source' => 'COUNT(DISTINCT O.idorder)'
			),
			'code' => Array(
				'source' => 'C.code'
			)
		));
		
		$datagrid->setFrom('
			coupons C 
			LEFT JOIN couponsview CV ON CV.couponsid = C.idcoupons
			LEFT JOIN couponstranslation CT ON C.idcoupons = CT.couponsid AND CT.languageid = :languageid
			LEFT JOIN `order` O ON C.idcoupons = O.couponid
		');
		
		$datagrid->setGroupBy('
			C.idcoupons
		');
		
		if (Helper::getViewId() > 0){
			$datagrid->setAdditionalWhere('
				CV.viewid = :viewid
			');
		}
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getCouponsForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteCoupons ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteCoupons'
		), $this->getName());
	}

	public function deleteCoupons ($id)
	{
		$ids = (is_array($id)) ? $id : (array) $id;
		DbTracker::deleteRows('coupons', 'idcoupons', $ids);
	}

	public function doAJAXEnableCoupons ($datagridId, $id)
	{
		try{
			$this->enableCoupons($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->registry->core->getMessage('ERR_UNABLE_TO_ENABLE_STATICBLOCKS')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function doAJAXDisableCoupons ($datagridId, $id)
	{
		try{
			$this->disableCoupons($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->registry->core->getMessage('ERR_UNABLE_TO_DISABLE_STATICBLOCKS')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function addCoupons ($Data)
	{
		$sql = 'INSERT INTO coupons SET
					discount = :discount,
					datefrom = :datefrom,
					dateto = :dateto,
					suffixtypeid = :suffixtypeid,
					globalqty = :globalqty,
					clientqty = :clientqty,
					code = :code,
					currencyid = :currencyid,
					minimumordervalue = :minimumordervalue,
					excludepromotions = :excludepromotions,
					freeshipping = :freeshipping';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('discount', $Data['discount']);
		if ($Data['datefrom'] != NULL && $Data['datefrom'] != '0000-00-00 00:00:00'){
			$stmt->bindValue('datefrom', $Data['datefrom']);
		}
		else{
			$stmt->bindValue('datefrom', NULL);
		}
		if ($Data['dateto'] != NULL && $Data['dateto'] != '0000-00-00 00:00:00'){
			$stmt->bindValue('dateto', $Data['dateto']);
		}
		else{
			$stmt->bindValue('dateto', NULL);
		}
		$stmt->bindValue('suffixtypeid', $Data['suffixtypeid']);
		$stmt->bindValue('globalqty', $Data['globalqty']);
		$stmt->bindValue('clientqty', $Data['clientqty']);
		$stmt->bindValue('currencyid', $Data['currencyid']);
		if (isset($Data['freeshipping']) && $Data['freeshipping'] == 1){
			$stmt->bindValue('freeshipping', 1);
		}
		else{
			$stmt->bindValue('freeshipping', 0);
		}
		if (isset($Data['excludepromotions']) && $Data['excludepromotions'] == 1){
			$stmt->bindValue('excludepromotions', 1);
		}
		else{
			$stmt->bindValue('excludepromotions', 0);
		}
		$stmt->bindValue('code', $Data['code']);
		$stmt->bindValue('minimumordervalue', $Data['minimumordervalue']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->registry->core->getMessage('ERR_NEWS_ADD'), 4, $e->getMessage());
		}
		
		$couponsid = Db::getInstance()->lastInsertId();
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO couponstranslation (couponsid,name, description, languageid)
						VALUES (:couponsid,:name, :description, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('couponsid', $couponsid);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('description', $Data['description'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->registry->core->getMessage('ERR_COUPONS_ADD'), 4, $e->getMessage());
			}
		}
		
		foreach ($Data['view'] as $key => $val){
			$sql = 'INSERT INTO couponsview (couponsid ,viewid)
					VALUES (:couponsid, :viewid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('couponsid', $couponsid);
			$stmt->bindValue('viewid', $val);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->registry->core->getMessage('ERR_NEWS_ADD'), 4, $e->getMessage());
			}
		}
		
		if (! empty($Data['category']) && is_array($Data['category'])){
			foreach ($Data['category'] as $key => $val){
				$sql = 'INSERT INTO couponscategory (couponid ,categoryid)
							VALUES (:couponid, :categoryid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('couponid', $couponsid);
				$stmt->bindValue('categoryid', $val);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->registry->core->getMessage('ERR_NEWS_ADD'), 4, $e->getMessage());
				}
			}
		}
		
		if (! empty($Data['clients']) && is_array($Data['clients'])){
			foreach ($Data['clients'] as $key => $val){
				$sql = 'INSERT INTO couponsclient (couponid ,clientid)
				VALUES (:couponid, :clientid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('couponid', $couponsid);
				$stmt->bindValue('clientid', $val);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->registry->core->getMessage('ERR_NEWS_ADD'), 4, $e->getMessage());
				}
			}
		}
		
		if (isset($Data['product']) && is_array($Data['product'])){
			foreach ($Data['product'] as $key => $val){
				$sql = 'INSERT INTO
						couponsproduct
					SET
					couponid = :couponid,
					productid = :productid';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('couponid', $couponsid);
				$stmt->bindValue('productid', $val);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->registry->core->getMessage('ERR_NEWS_ADD'), 4, $e->getMessage());
				}
			}
		}
		
		return true;
	}

	protected function getRandomCode ($name = '')
	{
		$charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$len = strlen($charset);
		
		$passwd = $name;
		for ($i = 0; $i < 5; ++ $i){
			$passwd .= $charset[rand(0, $len - 1)];
		}
		return $passwd;
	}

	public function generateCodes ($data)
	{
		$sql = 'SELECT code FROM coupons';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		
		foreach ($data['name'] as $k => $val){
			$data['description'][$k] = '';
		}
		
		$coupons = array();
		while ($rs = $stmt->fetch()){
			$coupons[] = $rs['code'];
		}
		
		$data['dateto'] = NULL;
		$data['datefrom'] = NULL;
		$data['globalqty'] = 1;
		$data['clientqty'] = 1;
		
		$data['qty'] = ($data['qty'] < 1) ? 1 : (int) $data['qty'];
		
		$i = 0;
		
		do{
			$code = $this->getRandomCode($data['prefix']);
			
			if (in_array($code, $coupons)){
				continue;
			}
			$data['code'] = $code;
			$this->addCoupons($data);
			
			++ $i;
		}
		while ($i < $data['qty']);
	}

	public function editCoupons ($Data, $id)
	{
		$sql = 'UPDATE coupons SET
						discount = :discount,
						datefrom = :datefrom,
						dateto = :dateto,
						suffixtypeid = :suffixtypeid,
						globalqty = :globalqty,
					clientqty = :clientqty,
						code = :code,
						currencyid = :currencyid,
						minimumordervalue = :minimumordervalue,
						excludepromotions = :excludepromotions,
						freeshipping = :freeshipping
					WHERE idcoupons = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('discount', $Data['discount']);
		if ($Data['datefrom'] != NULL && $Data['datefrom'] != '0000-00-00 00:00:00'){
			$stmt->bindValue('datefrom', $Data['datefrom']);
		}
		else{
			$stmt->bindValue('datefrom', NULL);
		}
		if ($Data['dateto'] != NULL && $Data['dateto'] != '0000-00-00 00:00:00'){
			$stmt->bindValue('dateto', $Data['dateto']);
		}
		else{
			$stmt->bindValue('dateto', NULL);
		}
		$stmt->bindValue('suffixtypeid', $Data['suffixtypeid']);
		$stmt->bindValue('globalqty', $Data['globalqty']);
		$stmt->bindValue('clientqty', $Data['clientqty']);
		$stmt->bindValue('currencyid', $Data['currencyid']);
		if (isset($Data['freeshipping']) && $Data['freeshipping'] == 1){
			$stmt->bindValue('freeshipping', 1);
		}
		else{
			$stmt->bindValue('freeshipping', 0);
		}
		if (isset($Data['excludepromotions']) && $Data['excludepromotions'] == 1){
			$stmt->bindValue('excludepromotions', 1);
		}
		else{
			$stmt->bindValue('excludepromotions', 0);
		}
		$stmt->bindValue('id', $id);
		$stmt->bindValue('code', $Data['code']);
		$stmt->bindValue('minimumordervalue', $Data['minimumordervalue']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->registry->core->getMessage('ERR_NEWS_ADD'), 4, $e->getMessage());
		}
		
		DbTracker::deleteRows('couponstranslation', 'couponsid', $id);
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO couponstranslation (couponsid,name, description, languageid)
					VALUES (:couponsid,:name, :description, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('couponsid', $id);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('description', $Data['description'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->registry->core->getMessage('ERR_COUPONS_ADD'), 4, $e->getMessage());
			}
		}
		
		DbTracker::deleteRows('couponsview', 'couponsid', $id);
		
		foreach ($Data['view'] as $key => $val){
			$sql = 'INSERT INTO couponsview (couponsid ,viewid)
					VALUES (:couponsid, :viewid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('couponsid', $id);
			$stmt->bindValue('viewid', $val);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->registry->core->getMessage('ERR_NEWS_ADD'), 4, $e->getMessage());
			}
		}
		
		DbTracker::deleteRows('couponscategory', 'couponid', $id);
		
		if (! empty($Data['category'])){
			foreach ($Data['category'] as $key => $val){
				$sql = 'INSERT INTO couponscategory (couponid ,categoryid)
						VALUES (:couponid, :categoryid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('couponid', $id);
				$stmt->bindValue('categoryid', $val);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->registry->core->getMessage('ERR_NEWS_ADD'), 4, $e->getMessage());
				}
			}
		}
		
		DbTracker::deleteRows('couponsclient', 'couponid', $id);
		
		if (! empty($Data['clients'])){
			foreach ($Data['clients'] as $key => $val){
				$sql = 'INSERT INTO couponsclient (couponid ,clientid)
						VALUES (:couponid, :clientid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('couponid', $id);
				$stmt->bindValue('clientid', $val);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->registry->core->getMessage('ERR_NEWS_ADD'), 4, $e->getMessage());
				}
			}
		}
		
		DbTracker::deleteRows('couponsproduct', 'couponid', $id);
		
		if (isset($Data['product']) && is_array($Data['product'])){
			foreach ($Data['product'] as $key => $val){
				$sql = 'INSERT INTO
						couponsproduct
					SET
					couponid = :couponid,
					productid = :productid';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('couponid', $id);
				$stmt->bindValue('productid', $val);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->registry->core->getMessage('ERR_NEWS_ADD'), 4, $e->getMessage());
				}
			}
		}
		
		return true;
	}

	public function getCouponsView ($id)
	{
		$sql = "SELECT
					discount,
					datefrom,
					dateto,
					suffixtypeid,
					globalqty,
					clientqty,
					code,
					currencyid,
					minimumordervalue,
					excludepromotions,
					freeshipping
				FROM coupons
				WHERE idcoupons = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'discount' => $rs['discount'],
				'datefrom' => $rs['datefrom'],
				'dateto' => $rs['dateto'],
				'suffixtypeid' => $rs['suffixtypeid'],
				'globalqty' => $rs['globalqty'],
				'clientqty' => $rs['clientqty'],
				'code' => $rs['code'],
				'minimumordervalue' => $rs['minimumordervalue'],
				'freeshipping' => $rs['freeshipping'],
				'excludepromotions' => $rs['excludepromotions'],
				'currencyid' => $rs['currencyid'],
				'language' => $this->getCouponsTranslation($id),
				'view' => $this->getCouponsViews($id),
				'category' => $this->getCategoryIds($id),
				'product' => $this->getProductId($id),
				'clients' => $this->getCouponsClients($id)
			);
		}
		return $Data;
	}

	public function getCouponsClients ($id)
	{
		$sql = "SELECT clientid
					FROM couponsclient
					WHERE couponid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['clientid'];
		}
		return $Data;
	}

	public function getCouponsViews ($id)
	{
		$sql = "SELECT 
					viewid
				FROM couponsview
				WHERE couponsid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['viewid'];
		}
		return $Data;
	}

	public function getCouponsTranslation ($id)
	{
		$sql = "SELECT
					name,
					description,
					languageid
				FROM couponstranslation
				WHERE couponsid = :id";
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

	public function getCategoryIds ($id)
	{
		$sql = 'SELECT
					categoryid AS id
				FROM couponscategory
				WHERE couponid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['id'];
		}
		return $Data;
	}

	public function getProductId ($id)
	{
		$sql = 'SELECT
					productid
				FROM
					couponsproduct
				WHERE
					couponid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['productid'];
		}
		return $Data;
	}

	public function getCouponSuffixTypesForSelect ()
	{
		$res = App::getModel('suffix')->getSuffixTypes();
		$Data = Array();
		foreach ($res as $value){
			if ($value['symbol'] != '=' && $value['symbol'] != '+'){
				$Data[$value['id']] = $value['symbol'];
			}
		}
		return $Data;
	}
}