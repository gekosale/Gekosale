<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms 
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the 
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 * 
 * $Revision: 309 $
 * $Author: gekosale $
 * $Date: 2011-08-01 21:10:16 +0200 (Pn, 01 sie 2011) $
 * $Id: invoice.php 309 2011-08-01 19:10:16Z gekosale $ 
 */
namespace Gekosale;

class AvailablityModel extends Component\Model\Datagrid
{

	public function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('availablity', Array(
			'idavailablity' => Array(
				'source' => 'A.idavailablity'
			),
			'name' => Array(
				'source' => 'AT.name'
			),
			'adddate' => Array(
				'source' => 'A.adddate'
			)
		));
		$datagrid->setFrom('
			`availablity` A
			LEFT JOIN availablitytranslation AT ON A.idavailablity = AT.availablityid AND AT.languageid = :languageid
		');
		
		$datagrid->setGroupBy('A.idavailablity');
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getAvailablityForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteAvailablity ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteAvailablity'
		), $this->getName());
	}

	public function deleteAvailablity ($id)
	{
		DbTracker::deleteRows('availablity', 'idavailablity', $id);
	}

	public function addNewAvailablity ($Data)
	{
		try{
			$newAvailablityId = $this->addAvailablity($Data);
		}
		catch (Exception $e){
			throw new CoreException($this->registry->core->getMessage('ERR_ORDER_STATUS_ADD'), 11, $e->getMessage());
		}
		return true;
	}

	public function addAvailablity ($Data)
	{
		$sql = 'INSERT INTO availablity (adddate) VALUES (NOW())';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		
		$availablityid = Db::getInstance()->lastInsertId();
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO availablitytranslation (availablityid, name, description, languageid)
					VALUES (:availablityid, :name, :description, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('availablityid', $availablityid);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('description', $Data['description'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->registry->core->getMessage('ERR_ORDERSTATUS_ADD'), 15, $e->getMessage());
			}
		}
		return $availablityid;
	}

	public function getAvailablityView ($id)
	{
		$Data = Array(
			'language' => $this->getAvailablityTranslation($id),
			'id' => $id
		);
		
		return $Data;
	}

	public function editAvailablity ($Data, $id)
	{
		try{
			$this->updateAvailablity($Data, $id);
		}
		catch (Exception $e){
			throw new CoreException($this->registry->core->getMessage('ERR_ORDER_STATUS_EDIT'), 125, $e->getMessage());
		}
		return true;
	}

	public function updateAvailablity ($Data, $id)
	{
		$sql = 'DELETE FROM availablitytranslation WHERE availablityid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		
		foreach ($Data['name'] as $key => $val){
			$sql = 'INSERT INTO availablitytranslation (availablityid,name,description, languageid)
					VALUES (:availablityid,:name,:description, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('availablityid', $id);
			$stmt->bindValue('name', $Data['name'][$key]);
			$stmt->bindValue('description', $Data['description'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->registry->core->getMessage('ERR_ORDERSTATUS_EDIT'), 129, $e->getMessage());
			}
		}
		return true;
	}

	public function getAvailablityAll ()
	{
		$sql = 'SELECT 
					AT.availablityid, 
					AT.name 
				FROM `availablitytranslation` AT
				LEFT JOIN availablity A ON AT.availablityid = A.idavailablity
				WHERE AT.languageid = :id
				ORDER BY AT.name ASC';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', App::getContainer()->get('session')->getActiveLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['availablityid'],
				'name' => $rs['name']
			);
		}
		return $Data;
	}

	public function getAvailablityToSelect ()
	{
		$Data = $this->getAvailablityAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return $tmp;
	}

	public function getAvailablityTranslation ($id)
	{
		$sql = "SELECT 
					name,
					description,
					languageid
				FROM availablitytranslation
				WHERE availablityid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$Data = Array();
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['languageid']] = Array(
				'name' => $rs['name'],
				'description' => $rs['description']
			);
		}
		return $Data;
	}

	public function getAvailablityForProductById ($id)
	{
		$sql = 'SELECT
				   availablityid
				FROM product 
				WHERE idproduct = :productid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productid', $id);
		$rs = $stmt->executeQuery();
		if ($rs->first()){
			return $rs['availablityid'];
		}
	}

	public function productBoxAssign ($event, $id)
	{
		$populate = Array();
		$sql = 'SELECT
				   	AT.name,
				   	AT.description,
					A.photoid
				FROM product P
				LEFT JOIN availablity A ON A.idavailablity = P.availablityid
				LEFT JOIN availablitytranslation AT ON AT.availablityid = P.availablityid AND AT.languageid = :languageid 
				WHERE P.idproduct = :productid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productid', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$rs = $stmt->executeQuery();
		if ($rs->first()){
			$populate = Array(
				'availablity' => Array(
					'name' => $rs['name'],
					'description' => $rs['description'],
					'photo' => App::getModel('gallery')->getImagePath(App::getModel('gallery')->getOrginalImageById($rs['photoid']))
				)
			);
		}
		$event->setReturnValues($populate);
	}
}