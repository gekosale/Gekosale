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
 * $Revision: 627 $
 * $Author: gekosale $
 * $Date: 2012-01-20 23:05:57 +0100 (Pt, 20 sty 2012) $
 * $Id: language.php 627 2012-01-20 22:05:57Z gekosale $
 */
namespace Gekosale\Plugin;

use xajaxResponse;

class LanguageModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('language', Array(
			'idlanguage' => Array(
				'source' => 'L.idlanguage'
			),
			'name' => Array(
				'source' => 'L.name'
			),
			'translation' => Array(
				'source' => 'L.translation',
				'processLanguage' => true
			),
			'currency' => Array(
				'source' => 'C.currencysymbol',
				'prepareForSelect' => true
			),
			'flag' => Array(
				'source' => 'L.flag'
			),
			'adddate' => Array(
				'source' => 'L.adddate'
			)
		));

		$datagrid->setFrom('
			language L
			LEFT JOIN languageview LV ON LV.languageid = L.idlanguage
			LEFT JOIN currency C ON L.currencyid = C.idcurrency
		');

		$datagrid->setGroupBy('
			L.idlanguage
		');

		$datagrid->setAdditionalWhere('
			IF(:viewid IS NOT NULL, LV.viewid = :viewid, 1)
		');
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getLanguageForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteLanguage ($id, $datagrid)
	{
		$objResponse = new xajaxResponse();
		$this->deleteLanguage($datagrid);
		$objResponse->script('window.location.reload(true)');
		return $objResponse;
	}

	public function deleteLanguage ($id)
	{
		DbTracker::deleteRows('language', 'idlanguage', $id);
		$this->flushCache();
	}

	public function getLanguageALL ()
	{
		$sql = 'SELECT
					idlanguage AS id,
					translation,
					name,
					flag
				FROM language';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'translation' => $this->trans($rs['translation']),
				'flag' => $rs['flag'],
				'name' => $rs['name']
			);
		}
		return $Data;
	}

	public function getLanguageALLToSelect ()
	{
		$Data = $this->getLanguageALL();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $this->trans($key['translation']);
		}
		return $tmp;
	}

	public function getLanguageView ($id)
	{
		$sql = "SELECT
					idlanguage AS id,
					name,
					translation,
					currencyid,
					flag
				FROM language
				WHERE idlanguage = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'id' => $rs['id'],
				'name' => $rs['name'],
				'currencyid' => $rs['currencyid'],
				'flag' => Array(
					'file' => $rs['flag']
				),
				'translation' => $rs['translation'],
				'view' => $this->LanguageView($id)
			);
		}
		return $Data;
	}

	public function LanguageView ($id)
	{
		$sql = "SELECT
					viewid
				FROM languageview
				WHERE languageid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['viewid'];
		}
		return $Data;
	}

	public function updateLanguage ($Data, $id)
	{
		$sql = 'UPDATE language SET
					name=:name,
					translation=:translation,
					currencyid=:currencyid,
					flag=:flag
				WHERE idlanguage =:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);
		$stmt->bindValue('translation', $Data['translation']);
		$stmt->bindValue('currencyid', $Data['currencyid']);
		if (isset($Data['flag']['file'])){
			$stmt->bindValue('flag', $Data['flag']['file']);
		}
		else{
			$stmt->bindValue('flag', NULL);
		}

		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_LANGUAGE_EDIT'), 13, $e->getMessage());
			return false;
		}
		return true;
	}

	public function editLanguage ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->updateLanguage($Data, $id);
			$this->updateLanguageView($Data['view'], $id);
			if (isset($Data['translations']['file']) && ($Data['translations']['file'] != '')){
				$this->importTranslationFromFile($Data['translations']['file'], $id);
			}
			$this->flushCache();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_DISPATCHMETHOD_EDIT'), 125, $e->getMessage());
		}

		Db::getInstance()->commit();
		return true;
	}

	public function updateLanguageView ($array, $id)
	{
		DbTracker::deleteRows('languageview', 'languageid', $id);

		foreach ($array as $value){
			$sql = 'INSERT INTO languageview (viewid, languageid)
						VALUES (:viewid, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('languageid', $id);
			$stmt->bindValue('viewid', $value);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function addLanguage ($Data)
	{
		$sql = 'INSERT INTO language SET
					name = :name,
					translation = :translation,
					currencyid=:currencyid,
					flag=:flag
				';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);
		$stmt->bindValue('translation', $Data['translation']);
		$stmt->bindValue('currencyid', $Data['currencyid']);
		if (isset($Data['flag']['file'])){
			$stmt->bindValue('flag', $Data['flag']['file']);
		}
		else{
			$stmt->bindValue('flag', NULL);
		}

		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_LANGUAGE_ADD'), 14, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addNewLanguage ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newLanguageId = $this->addLanguage($Data);
			$this->addLanguageView($Data['view'], $newLanguageId);
			if (isset($Data['copylanguage']) && ($Data['copylanguage'] > 0)){
				$this->addLanguageTranslation($Data['copylanguage'], $newLanguageId);
				$this->copyLayoutBoxTranslation($Data['copylanguage'], $newLanguageId);
			}
			if (isset($Data['translations']['file']) && ($Data['translations']['file'] != '')){
				$this->importTranslationFromFile($Data['translations']['file'], $newLanguageId);
			}
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_LANGUAGE_ADD'), 11, $e->getMessage());
		}

		Db::getInstance()->commit();
		App::getContainer()->get('cache')->delete('languages');
		return true;
	}

	protected function addLanguageTranslation ($copyfrom, $copyto)
	{
		@set_time_limit(0);
		$sql = 'SELECT translation, translationid FROM translationdata WHERE languageid = :languageid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', $copyfrom);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$sql2 = 'INSERT INTO translationdata SET
						translation = :translation,
						translationid = :translationid,
						languageid = :languageid
					';
			$stmt2 = Db::getInstance()->prepare($sql2);
			$stmt2->bindValue('translation', $rs['translation']);
			$stmt2->bindValue('translationid', $rs['translationid']);
			$stmt2->bindValue('languageid', $copyto);
			$stmt2->execute();
		}
	}

	protected function copyLayoutBoxTranslation ($copyfrom, $copyto)
	{
		@set_time_limit(0);
		$sql = 'SELECT layoutboxid, title FROM layoutboxtranslation WHERE languageid = :languageid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', $copyfrom);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$sql2 = 'INSERT INTO layoutboxtranslation SET
						layoutboxid = :layoutboxid,
						title = :title,
						languageid = :languageid
					';
			$stmt2 = Db::getInstance()->prepare($sql2);
			$stmt2->bindValue('title', $rs['title']);
			$stmt2->bindValue('layoutboxid', $rs['layoutboxid']);
			$stmt2->bindValue('languageid', $copyto);
			$stmt2->execute();
		}
	}

	public function importTranslationFromFile ($file, $languageid)
	{
		@set_time_limit(0);
		try{
			$xml = simplexml_load_file(ROOTPATH . 'upload/' . $file);
			foreach ($xml->row as $row){
				$name = (string) $row->field[0];
				$value = (string) $row->field[1];

				$this->updateTranslation($languageid, $name, $value, TRUE);
			}
			App::getContainer()->get('cache')->delete('translations');
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_LANGUAGE_IMPORT'), 11, $e->getMessage());
		}
	}

	/**
	 * Update translation for specified key=$name to specified
	 * translation=$value
	 *
	 * @param type $languageid
	 *        	id of language
	 * @param type $name
	 *        	translation key
	 * @param type $value
	 *        	translation
	 * @param type $updateOnExists
	 *        	if true existing translations also will be modified
	 */
	public function updateTranslation ($languageid, $name, $value, $updateOnExists = false)
	{
		$sql = 'SELECT idtranslation as id FROM translation WHERE name = :name';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $name);
		$stmt->execute();
		$rs = $stmt->fetch();

		if ($rs && $updateOnExists == false)
			return false;

		if ($rs){
			$id = $rs['id'];
			$sql = 'INSERT INTO translationdata SET
						translationid = :translationid,
						languageid = :languageid,
						translation = :translation
					ON DUPLICATE KEY UPDATE
						translation = :translation';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('translation', $value);
			$stmt->bindValue('translationid', $id);
			$stmt->bindValue('languageid', $languageid);
			$stmt->execute();
		}
		else{
			$sql = 'INSERT INTO translation SET name = :name';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $name);

			$stmt->execute();
			$id = Db::getInstance()->lastInsertId();
			$sql = 'INSERT INTO translationdata SET
						translationid = :translationid,
						languageid = :languageid,
						translation = :translation
					ON DUPLICATE KEY UPDATE
						translation = :translation';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('translation', $value);
			$stmt->bindValue('translationid', $id);
			$stmt->bindValue('languageid', $languageid);
			$stmt->execute();
		}

		return true;
	}

	protected function addLanguageView ($Data, $id)
	{
		foreach ($Data as $key => $viewid){
			$sql = 'INSERT INTO languageview (viewid, languageid)
					VALUES (:viewid, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('languageid', $id);
			$stmt->bindValue('viewid', $viewid);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function changeLanguage ($lang, $reload = false)
	{
		$objResponse = new xajaxResponse();
		App::getContainer()->get('session')->setActiveMenuData(NULL);
		$sql = 'SELECT name FROM language WHERE idlanguage = :lang';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('lang', $lang);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			App::getContainer()->get('session')->setActiveLanguage($rs['name']);
			App::getContainer()->get('session')->setActiveLanguageId($lang);
		}
		if ($reload == true){
			$objResponse->script('window.location.reload(true)');
		}
		else{
			$objResponse->script('theDatagrid.LoadData();');
		}
		return $objResponse;
	}

	public function getLanguages ()
	{
		$sql = 'SELECT
					idlanguage AS id,
					flag,
					translation,
					viewid
				FROM language L
				LEFT JOIN languageview LV ON LV.languageid = L.idlanguage';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = Array(
				'id' => $rs['id'],
				'flag' => $rs['flag'],
				'weight' => $rs['id'],
				'icon' => $rs['flag'],
				'name' => $this->trans($rs['translation'])
			);
		}
		return $Data;
	}

	public function flushCache ()
	{
		App::getContainer()->get('cache')->delete('languages');
		App::getContainer()->get('cache')->delete('translations');
	}
}