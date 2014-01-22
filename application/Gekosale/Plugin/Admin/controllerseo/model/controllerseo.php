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
 * $Id: controllerseo.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Plugin;

class ControllerSeoModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('controllerseo', Array(
			'idcontroller' => Array(
				'source' => 'C.idcontroller'
			),
			'name' => Array(
				'source' => 'C.name'
			),
			'translation' => Array(
				'source' => 'CS.name',
				'prepareForAutosuggest' => true
			)
		));
		
		$datagrid->setFrom('
			controller C
			LEFT JOIN controllerseo CS ON C.idcontroller = CS.controllerid AND CS.languageid = :languageid
			LEFT JOIN language L ON L.idlanguage = CS.languageid 
		');
		
		$datagrid->setAdditionalWhere('
			mode = 0
		');
	
	}

	public function getTranslationNameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('translation', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getControllerSeoForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXUpdateControllerSeo ($id, $name)
	{
		$sql = 'DELETE FROM controllerseo WHERE controllerid = :controllerid AND languageid = :languageid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('controllerid', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		
		$sql = 'INSERT INTO controllerseo SET 
					name=:name,
					controllerid = :controllerid,
					languageid = :languageid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $name);
		$stmt->bindValue('controllerid', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$this->flushCache();
	}

	public function getControllerSeoAll ()
	{
		$sql = 'SELECT name as translation FROM controllerseo WHERE languageid = :languageid';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'translation' => $rs['translation']
			);
		}
		return $Data;
	}

	public function getControllerSeoView ($id)
	{
		$sql = "SELECT 
					name, 
					enable, 
					IF(mode  = 0, 2, 1) AS mode
				FROM controller
				WHERE idcontroller = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'name' => $rs['name'],
				'enable' => $rs['enable'],
				'mode' => $rs['mode'],
				'translation' => $this->getControllerTranslation($id)
			);
		}
		return $Data;
	}

	public function getControllerTranslation ($id)
	{
		$sql = "SELECT name as translation, languageid
					FROM controllerseo
					WHERE controllerid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['languageid']] = Array(
				'translation' => $rs['translation']
			);
		}
		return $Data;
	}

	public function updateControllerSeo ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->editController($Data, $id);
			$this->editControllerSeo($Data, $id);
			$this->flushCache();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CONTROLLER_EDIT'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function editController ($Data, $id)
	{
		$sql = 'UPDATE controller 
					SET name=:name
				WHERE idcontroller = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('name', $Data['controller']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CONTROLLER_UPDATE'), 1, $e->getMessage());
		}
		return true;
	}

	public function editControllerSeo ($Data, $id)
	{
		DbTracker::deleteRows('controllerseo', 'controllerid', $id);
		
		foreach ($Data['translation'] as $key => $val){
			$sql = 'INSERT INTO controllerseo (name, languageid, controllerid)
					VALUES (:name, :languageid, :controllerid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $Data['translation'][$key]);
			$stmt->bindValue('languageid', $key);
			$stmt->bindValue('controllerid', $id);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CONTROLLER_SEO_TRANSLATION_EDIT'), 13, $e->getMessage());
			}
		}
		return true;
	}

	public function flushCache ()
	{
		App::getContainer()->get('cache')->delete('categories');
		App::getContainer()->get('cache')->delete('news');
		App::getContainer()->get('cache')->delete('seocontrollers');
	}

}