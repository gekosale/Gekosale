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
 * $Id: translation.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Plugin;

class TranslationModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('translation', Array(
			'idtranslation' => Array(
				'source' => 'T.idtranslation'
			),
			'name' => Array(
				'source' => 'T.name',
				'prepareForAutosuggest' => true
			),
			'translation' => Array(
				'source' => 'TD.translation',
				'prepareForAutosuggest' => true
			),
			'adddate' => Array(
				'source' => 'T.adddate'
			),
		));
		
		$datagrid->setFrom('
			translation T
			LEFT JOIN translationdata TD ON T.idtranslation = TD.translationid AND TD.languageid = :languageid
			LEFT JOIN language L ON L.idlanguage = TD.languageid 
		');
	
	}

	public function getNameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
	}

	public function getTranslationNameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('translation', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getTranslationForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteTranslation ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteTranslation'
		), $this->getName());
	}

	public function deleteTranslation ($id)
	{
		DbTracker::deleteRows('translation', 'idtranslation', $id);
	}

	public function doAJAXUpdateTranslation ($id, $translation)
	{
		Db::getInstance()->beginTransaction();
		
		$sql = 'DELETE FROM translationdata WHERE translationid = :translationid AND languageid = :languageid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('translationid', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		
		$sql = 'INSERT INTO translationdata SET 
					translation=:translation,
					translationid = :translationid,
					languageid = :languageid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('translationid', $id);
		$stmt->bindValue('translation', $translation);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		
		Db::getInstance()->commit();
		
		$this->flushCacheTranslations();
		
		return true;
	}

	public function getTranslationView ($id)
	{
		$sql = "SELECT idtranslation AS id, name FROM translation
				WHERE idtranslation = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'id' => $rs['id'],
				'name' => $rs['name'],
				'language' => $this->getTranslation($id)
			);
		}
		return $Data;
	}

	public function getTranslation ($id)
	{
		$sql = "SELECT translation,languageid
					FROM translationdata
					WHERE translationid =:id";
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

	public function editTranslation ($Data, $id)
	{
		$sql = 'UPDATE translation SET name=:name WHERE idtranslation = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);
		
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
			$this->flushCacheTranslations();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_TRANSLATION_EDIT'), 13, $e->getMessage());
			return false;
		}
		
		DbTracker::deleteRows('translationdata', 'translationid', $id);
		
		foreach ($Data['translation'] as $key => $val){
			$sql = 'INSERT INTO translationdata (translationid,translation, languageid)
						VALUES (:translationid,:translation,:languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('translationid', $id);
			$stmt->bindValue('translation', $Data['translation'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_TRANSLATION_EDIT'), 13, $e->getMessage());
			}
		}
		return true;
	}

	public function addTranslation ($Data)
	{
		$sql = 'INSERT INTO translation (name) VALUES (:name)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_TRANSLATION_ADD'), 14, $e->getMessage());
		}
		
		$translationid = Db::getInstance()->lastInsertId();
		
		foreach ($Data['translation'] as $key => $val){
			$sql = 'INSERT INTO translationdata (translationid,translation, languageid)
						VALUES (:translationid,:translation,:languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('translationid', $translationid);
			$stmt->bindValue('translation', $Data['translation'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_TRANSLATION_ADD'), 14, $e->getMessage());
			}
		}
		$this->flushCacheTranslations();
		return $translationid;
	}

	public function flushCacheTranslations ()
	{
		App::getContainer()->get('cache')->delete('translations');
	}
}