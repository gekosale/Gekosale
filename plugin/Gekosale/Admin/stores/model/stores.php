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
 * $Id: stores.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale;

class StoresModel extends Component\Model
{

	public function getViewsAll ()
	{
		$sql = 'SELECT 
					V.idview AS id,
					V.name,
					V.storeid
				FROM view V
				WHERE V.idview IN (' . implode(',', $this->getViewForHelperAll()) . ')
				ORDER BY V.name ASC
		';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = Array(
				'id' => $rs['id'],
				'name' => $rs['name'],
				'parent' => $rs['storeid']
			);
		}
		return $Data;
	}

	public function getStoresAll ()
	{
		$sql = 'SELECT 
					S.idstore AS id,
					S.shortcompanyname
				FROM store S
				ORDER BY S.shortcompanyname ASC';
		
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = Array(
				'id' => $rs['id'],
				'name' => $rs['shortcompanyname']
			);
		}
		return $Data;
	}

	public function getStoreViews ($storeid)
	{
		$sql = 'SELECT 
					idview AS id,
					name
				FROM view 
				WHERE storeid = :storeid
			';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('storeid', $storeid);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = Array(
				'name' => $rs['name'],
				'hasChildren' => 0
			);
		}
		return $Data;
	}

	public function getActiveStoreId ()
	{
		return (! is_null(Helper::getStoreId())) ? Helper::getStoreId() : 0;
	}

	public function getActiveLayer ()
	{
		$storeid = (! is_null(Helper::getStoreId())) ? Helper::getStoreId() : 0;
		$viewid = (! is_null(Helper::getViewId())) ? Helper::getViewId() : 0;
		if ($viewid > 0){
			return $storeid . '_' . $viewid;
		}
		else{
			return $storeid;
		}
	}

	public function changeActiveLayer ($layers, $hasDg = false)
	{
		$objResponse = new xajaxResponse();
		$layer = explode('_', $layers);
		if (! empty($layer[1])){
			$storeid = $layer[0];
			$viewid = $layer[1];
			Helper::setStoreId($storeid);
			Helper::setViewId($viewid);
		}
		else{
			Helper::setStoreId($layers);
			Helper::setViewId(null);
		}
		if ($hasDg == true){
			$objResponse->script('theDatagrid.LoadData();');
		}
		else{
			$objResponse->script('window.location.reload(true)');
		}
		
		return $objResponse;
	}

	public function changeActiveStoreId ($id)
	{
		$objResponse = new xajaxResponse();
		Helper::setStoreId($id);
		$objResponse->script('window.location.reload(true)');
		return $objResponse;
	}

	public function getViewForHelperAll ()
	{
		$globaluser = App::getContainer()->get('session')->getActiveUserIsGlobal();
		
		$Data = App::getContainer()->get('session')->getActiveViewIds();
		
		if ($Data == NULL){
			if ($globaluser == 1){
				
				$sql = 'SELECT 
							V.idview AS id
						FROM view V
						GROUP BY V.idview
					';
				
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->execute();
				while ($rs = $stmt->fetch()){
					$Data[] = $rs['id'];
				}
			}
			else{
				
				$sql = 'SELECT
							UGV.viewid
						FROM usergroupview UGV 
						lEFT JOIN view V ON UGV.viewid = V.idview
						lEFT JOIN store S ON V.storeid = S.idstore
						WHERE UGV.userid = :userid
						GROUP BY UGV.viewid';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('userid', App::getContainer()->get('session')->getActiveUserid());
				$stmt->execute();
				while ($rs = $stmt->fetch()){
					$Data[] = $rs['viewid'];
				}
			}
			App::getContainer()->get('session')->setActiveViewIds($Data);
		}
		return $Data;
	}
}