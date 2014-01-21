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
 * $Id: pagescheme.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale;

class PageschemeModel extends Component\Model
{

	public function getPageschemeAll ()
	{
		$sql = 'SELECT 
					idpagescheme AS id, 
					name,
					templatefolder
				FROM pagescheme';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $rs['name'],
				'templatefolder' => $rs['templatefolder']
			);
		}
		return $Data;
	}

	public function getPageschemeAllToSelect ()
	{
		$Data = $this->getPageschemeAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return $tmp;
	}

	public function addNewPageScheme ($submitedData)
	{
		Db::getInstance()->beginTransaction();
		try{
			$idNewPageScheme = $this->addPageScheme($submitedData);
			if ($idNewPageScheme != 0){
				App::getModel('fieldgenerator/fieldgenerator')->SaveCSSValues($idNewPageScheme, $submitedData, null, Array(
					$this,
					'addNewAttributeCss'
				), Array(
					$this,
					'addNewAttributeCssValue'
				), Array(
					$this,
					'addNewAttributeCss2ndValue'
				));
			}
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function addPageScheme ($submitedData)
	{
		$sql = 'INSERT INTO pagescheme (name, templatefolder) VALUES (:name, :templatefolder)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $submitedData['name']);
		$stmt->bindValue('templatefolder', $submitedData['templatefolder']);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PAGE_SCHEME_ADD'), 11, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addNewAttributeCss ($idNewPageScheme, $attribute, $selector, $class = NULL)
	{
		$sql = 'INSERT INTO pageschemecss (selector, attribute, pageschemeid) 
				VALUES (:selector, :attribute, :pageschemeid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('selector', $selector);
		$stmt->bindValue('attribute', $attribute);
		$stmt->bindValue('pageschemeid', $idNewPageScheme);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PAGE_SCHEME_ADD'), 11, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addNewAttributeCssValue ($idNewPageScheme, $newAttrCssId, $name, $value)
	{
		$sql = 'INSERT INTO pageschemecssvalue (pageschemeid, pageschemecssid, name, value)	
				VALUES (:pageschemeid, :pageschemecssid, :name, :value)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('pageschemeid', $idNewPageScheme);
		$stmt->bindValue('pageschemecssid', $newAttrCssId);
		$stmt->bindValue('name', $name);
		$stmt->bindValue('value', $value);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PAGE_SCHEME_ADD'), 11, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addNewAttributeCss2ndValue ($idNewPageScheme, $newAttrCssId, $name, $value, $value2)
	{
		$sql = 'INSERT INTO pageschemecssvalue (pageschemeid, pageschemecssid, name, value, secondvalue) 
				VALUES (:pageschemeid, :pageschemecssid, :name, :value, :secondvalue)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('pageschemeid', $idNewPageScheme);
		$stmt->bindValue('pageschemecssid', $newAttrCssId);
		$stmt->bindValue('name', $name);
		$stmt->bindValue('value', $value);
		$stmt->bindValue('secondvalue', $value2);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PAGE_SCHEME_ADD'), 11, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function getTemplateNameToEdit ($idPageScheme)
	{
		$sql = 'SELECT 
					PS.name,
					PS.templatefolder
				FROM pagescheme PS
				WHERE PS.idpagescheme= :idpagescheme';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idpagescheme', $idPageScheme);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data = Array(
				'name' => $rs['name'],
				'templatefolder' => $rs['templatefolder']
			);
		}
		return $Data;
	}

	public function getTemplateCssToEdit ($idPageScheme)
	{
		$sql = "SELECT 
					PSC.idpageschemecss, 
					PSC.class, 
					PSC.selector, 
					PSC.attribute
				FROM pageschemecss PSC
				WHERE PSC.pageschemeid = :idpagescheme";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idpagescheme', $idPageScheme);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['selector']][$rs['attribute']] = $this->getTemplateCssValueToEdit($rs['idpageschemecss']);
		}
		return $Data;
	}

	public function prepareFieldName ($class = NULL, $selector, $attribute)
	{
		$fieldName = '';
		if ($selector != NULL && $attribute != NULL){
			if ($class !== NULL){
				$prepareName = $class . ',' . $selector . '_' . $attribute;
			}
			else{
				$prepareName = $selector . '_' . $attribute;
			}
			$fieldName = $prepareName;
		}
		return $fieldName;
	}

	public function getTemplateCssValueToEdit ($pageschemecssid)
	{
		$sql = "SELECT
					PSCV.idpageschemecssvalue, 
					PSCV.pageschemeid, 
					PSCV.pageschemecssid, 
					PSCV.name, 
					PSCV.value, 
					PSCV.secondvalue
				FROM pageschemecssvalue PSCV
				WHERE PSCV.pageschemecssid= :pageschemecssid";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('pageschemecssid', $pageschemecssid);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			if ($rs['secondvalue'] != NULL){
				$Data[$rs['name']][$rs['value']] = $rs['secondvalue'];
			}
			else{
				$Data[$rs['name']] = $rs['value'];
			}
		}
		return $Data;
	}

	public function editPageScheme ($SubmitedData, $idpagescheme)
	{
		Db::getInstance()->beginTransaction();
		$cssValues = $this->DeletePageSchemeTemplateCssValue($idpagescheme);
		$css = $this->DeletePageSchemeTemplateCss($idpagescheme);
		App::getModel('fieldgenerator/fieldgenerator')->SaveCSSValues($idpagescheme, $SubmitedData, null, Array(
			$this,
			'addNewAttributeCss'
		), Array(
			$this,
			'addNewAttributeCssValue'
		), Array(
			$this,
			'addNewAttributeCss2ndValue'
		));
		Db::getInstance()->commit();
	}

	public function DeletePageSchemeTemplateCss ($id)
	{
		DbTracker::deleteRows('pageschemecss', 'pageschemeid', $id);
	}

	public function DeletePageSchemeTemplateCssValue ($id)
	{
		DbTracker::deleteRows('pageschemecssvalue', 'pageschemeid', $id);
	}

	public function saveTemplates ($Data)
	{
		foreach ($Data as $key => $file){
			if (isset($file['path']) && is_file($file['path']) && isset($file['content'])){
				file_put_contents($file['path'], $file['content']);
			}
		}
	}

	public function getFiles ($theme)
	{
		$path = ROOTPATH . 'themes' . DS . $theme . DS . 'assets' . DS . 'css';
		
		$exclude = Array(
			'scheme.less'
		);
		
		$inRoot = false;
		$files = Array();
		$dirs = Array();
		if (($dir = opendir($path)) === false){
			throw new Exception('Directory "' + $path + '" cannot be listed.');
		}
		while (($file = readdir($dir)) !== false){
			if ($file == '.'){
				continue;
			}
			if ($inRoot && ($file == '..')){
				continue;
			}
			$filepath = $path . DS . $file;
			if (! is_dir($filepath)){
				if (in_array(pathinfo($filepath, PATHINFO_EXTENSION), Array(
					'css',
					'less'
				))){
					$content = file_get_contents($path . DS . $file);
					if (! in_array($file, $exclude)){
						$files[] = Array(
							'name' => current(explode('.', $file)) . sha1(session_id()),
							'filename' => $file,
							'path' => $filepath,
							'mtime' => date('Y-m-d H:i:s', filemtime($filepath)),
							'content' => $content
						);
					}
				}
			}
		}
		
		closedir($dir);
		return $files;
	}
}