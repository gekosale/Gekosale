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
namespace Gekosale;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class TemplateEditorModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('pagescheme', Array(
			'idpagescheme' => Array(
				'source' => 'PS.idpagescheme'
			),
			'name' => Array(
				'source' => 'PS.name'
			),
			'templatefolder' => Array(
				'source' => 'PS.templatefolder'
			),
			'thumb' => Array(
				'source' => 'PS.templatefolder',
				'processFunction' => Array(
					$this,
					'getThumbnail'
				)
			),
			'def' => Array(
				'source' => 'PS.idpagescheme',
				'processFunction' => Array(
					$this,
					'checkDefault'
				)
			)
		));
		$datagrid->setFrom('
			pagescheme PS
		');
		
		$datagrid->setGroupBy('
			PS.idpagescheme
		');
	}

	public function editDefaultTemplate ($theme)
	{
		$themes = array(
			'wellcommerce_tech',
			'wellcommerce_fashion'
		);
		
		if (! in_array($theme, $themes)){
			return false;
		}
		
		$newThemeDir = ROOTPATH . 'themes' . DS . $theme . '_copy';
		if (is_dir($newThemeDir)){
			return true;
		}
		
		if (! @mkdir($newThemeDir)){
			throw new CoreException('Can\'t create directory: ' . $newThemeDir);
		}
		
		$dirLength = strlen(ROOTPATH . 'themes' . DS . $theme);
		
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ROOTPATH . 'themes' . DS . $theme), RecursiveIteratorIterator::SELF_FIRST);
		
		foreach ($files as $file){
			if ($file->isDir()){
				if (! is_dir($newThemeDir . substr($file->getPathname(), $dirLength)) && ! @mkdir($newThemeDir . substr($file->getPathname(), $dirLength))){
					throw new CoreException('Can\'t create directory: ' . $newThemeDir . substr($file->getPathname(), $dirLength));
				}
				continue;
			}
			
			if ($file->isFile()){
				if (! @copy($file->getPathname(), $newThemeDir . substr($file->getPathname(), $dirLength))){
					throw new CoreException('Can\'t copy filedirectory: ' . $newThemeDir . substr($file->getPathname(), $dirLength));
				}
				continue;
			}
		}
		
		Db::getInstance()->beginTransaction();
		
		$sql = 'SELECT name FROM pagescheme WHERE templatefolder = :templatefolder';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('templatefolder', $theme);
		$stmt->execute();
		$name = $stmt->fetchColumn() . ' - Kopia';
		
		$importData = json_decode(file_get_contents($newThemeDir . DS . 'settings' . DS . 'export.json'), true);
		
		$pageSchemeId = App::getModel('pagescheme')->addPageScheme(Array(
			'name' => $name,
			'templatefolder' => $theme . '_copy'
		));
		
		$import = App::getModel('pagescheme/import');
		$import->savePageScheme($importData['pagescheme'], $pageSchemeId);
		$import->saveBoxes($importData['boxes'], $pageSchemeId);
		$import->saveSubpages($importData['layouts'], $pageSchemeId);
		
		Db::getInstance()->commit();
		
		// default theme
		$this->doAJAXDefaultPagescheme(null, $pageSchemeId);
		
		return true;
	}

	public function isValidTplFile ($filePath)
	{
		static $path = NULL;
		
		if ($path === NULL){
			$parts = array_reverse(explode('.', $this->registry->core->getParam()));
			$theme = array_pop($parts);
			$path = rtrim(ROOTPATH . 'themes' . DS . $theme . DS . 'templates' . DS . implode(DS, array_reverse($parts)), DS) . DS;
		}
		
		$dir = dirname(realpath($filePath)) . DS;
		$file = basename($filePath);
		
		// wyjscie ponad katalog
		if ($path !== $dir){
			throw new CoreException(sprintf('Can\'t save template file %s', $filePath, $path));
		}
		
		// bledne rozszerzenie
		if (pathinfo($file, PATHINFO_EXTENSION) !== 'tpl'){
			throw new CoreException(sprintf('Can\'t save template file %s, bad extension', $filePath));
		}
		
		// plik nie istnieje
		if (! is_file($path . $file)){
			throw new CoreException(sprintf('Can\'t save template file %s, file not exists', $filePath));
		}
		
		return TRUE;
	}

	public function getValueForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getPageschemeForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeletePagescheme ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deletePagescheme'
		), $this->getName());
	}

	public function doAJAXUpdateScheme ($id, $name)
	{
		$sql = 'UPDATE pagescheme SET
					name = :name
				WHERE idpagescheme = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('name', $name);
		$stmt->execute();
		return 1;
	}

	public function doAJAXDefaultPagescheme ($datagridId, $id)
	{
		$sql = 'UPDATE view SET pageschemeid = :id WHERE idview = :viewid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('viewid', Helper::getViewId());
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PAGE_SCHEME_ADD'), 11, $e->getMessage());
		}
		
		App::getModel('pagescheme/import')->clearCache(ROOTPATH . DS . 'serialization', false);
		App::getModel('pagescheme/import')->clearCache(ROOTPATH . DS . 'cache', false);
		
		return $this->getDatagrid()->refresh($datagridId);
	}

	public function deletePagescheme ($id)
	{
		$theme = App::getModel('pagescheme')->getTemplateNameToEdit($id);
		
		if (empty($theme)){
			throw new CoreException('Unknown ID: ' . $id);
		}
		
		if (is_dir(ROOTPATH . 'themes' . DS . $theme['templatefolder'])){
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ROOTPATH . 'themes' . DS . $theme['templatefolder']), RecursiveIteratorIterator::CHILD_FIRST);
			foreach ($files as $file){
				if (in_array($file->getBasename(), array(
					'.',
					'..'
				))){
					continue;
				}
				
				if ($file->isDir()){
					if (! rmdir($file->getPathname())){
						throw new CoreException('Can\'t remove: ' . $file->getPathname());
					}
					continue;
				}
				
				if (! unlink($file->getPathname())){
					throw new CoreException('Can\'t remove: ' . $file->getPathname());
				}
			}
			
			@rmdir(ROOTPATH . 'themes' . DS . $theme['templatefolder']);
		}
		DbTracker::deleteRows('pagescheme', 'idpagescheme', $id);
	}

	public function checkDefault ($id)
	{
		$sql = 'SELECT pageschemeid FROM view WHERE idview = :viewid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			return ($rs['pageschemeid'] == $id) ? 1 : 0;
		}
		return 0;
	}

	public function getThumbnail ($path)
	{
		if (is_file(ROOTPATH . 'themes' . DS . $path . DS . 'info' . DS . 'thumbnail.png')){
			return App::getURLForAssetDirectory() . $path . '/info/thumbnail.png';
		}
	}

	public function getMainInfo ()
	{
		$this->xmlParser = new XmlParser();
		$pageschemes = App::getModel('pagescheme')->getPageschemeAll();
		$dirs = Array();
		foreach ($pageschemes as $pagescheme){
			$this->xmlParser->parseFast(ROOTPATH . 'themes/' . $pagescheme['templatefolder'] . '/info/info.xml');
			$info = $this->xmlParser->getValue('template', true);
			$dirs[] = Array(
				'name' => $pagescheme['name'],
				'id' => $pagescheme['id'],
				'info' => $info,
				'templatefolder' => $pagescheme['templatefolder'],
				'path' => App::getURLForAssetDirectory() . $pagescheme['templatefolder'],
				'active' => ($this->registry->loader->getParam('pageschemeid') == $pagescheme['id']) ? 1 : 0
			);
		}
		return $dirs;
	}

	public function getDirs ()
	{
		$parts = array_reverse(explode('.', $this->registry->core->getParam()));
		$theme = array_pop($parts);
		
		$dirs = Array();
		foreach (glob(ROOTPATH . 'themes/*', GLOB_ONLYDIR) as $dir){
			$i = 0;
			$dir = basename($dir);
			if ($dir == $theme){
				$dirs[$dir] = Array(
					'name' => $dir,
					'parent' => NULL,
					'weight' => 0
				);
				foreach (glob(ROOTPATH . 'themes' . DS . $dir . DS . 'templates' . DS . '*', GLOB_ONLYDIR) as $subdir){
					$j = 0;
					$subdir = basename($subdir);
					$dirs[$dir . '.' . $subdir] = Array(
						'name' => $subdir,
						'parent' => $dir,
						'weight' => $i
					);
					$i ++;
					foreach (glob(ROOTPATH . 'themes' . DS . $dir . DS . 'templates' . DS . $subdir . DS . '*', GLOB_ONLYDIR) as $subsubdir){
						$subsubdir = basename($subsubdir);
						$dirs[$dir . '.' . $subdir . '.' . $subsubdir] = Array(
							'name' => $subsubdir,
							'parent' => $dir . '.' . $subdir,
							'weight' => $j
						);
						$j ++;
					}
				}
			}
		}
		return $dirs;
	}

	public function getFiles ($id)
	{
		// $files = Array();
		$parts = array_reverse(explode('.', $id));
		$theme = array_pop($parts);
		$path = ROOTPATH . 'themes' . DS . $theme . DS . 'templates' . DS . implode(DS, array_reverse($parts));
		
		$inRoot = false;
		$files = Array();
		$dirs = Array();
		if (($dir = @opendir($path)) === false){
			throw new CoreException('Directory "' + $path + '" cannot be listed.');
		}
		while (($file = readdir($dir)) !== false){
			if ($file == '.'){
				continue;
			}
			if ($inRoot && ($file == '..')){
				continue;
			}
			$filepath = $path . DS . $file;
			if (is_file($filepath)){
				if (in_array(pathinfo($filepath, PATHINFO_EXTENSION), Array(
					'tpl'
				))){
					$content = file_get_contents($path . DS . $file);
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
		
		closedir($dir);
		return $files;
	}

	public function saveTemplates ($Data)
	{
		foreach ($Data as $file){
			if (is_array($file) && isset($file['path']) && isset($file['content']) && $this->isValidTplFile($file['path'])){
				@file_put_contents($file['path'], $file['content']);
			}
		}
	}

	public function SaveFileContent ($request)
	{
		$file = ROOTPATH . 'themes' . DS . $this->getParam() . DS . $request['file'];
		
		$extension = pathinfo($file, PATHINFO_EXTENSION);
		
		$extensions = Array(
			'less',
			'css',
			'tpl',
			'js',
			'xml',
			'json'
		);
		
		if (is_file($file) && in_array(pathinfo($file, PATHINFO_EXTENSION), $extensions)){
			@file_put_contents($file, $request['content']);
		}
		
		return Array(
			'msg' => "Plik {$request['file']} został zapisany."
		);
	}

	public function GetFileContent ($request)
	{
		$extensions = Array(
			'less',
			'css',
			'tpl',
			'js',
			'xml',
			'json'
		);
		
		$file = ROOTPATH . 'themes' . DS . $this->getParam() . DS . $request['file'];
		
		if (! (strpos($file, '..') === false && is_file($file))){
			throw new CoreException('File not "' . $file . '" found');
		}
		
		$extension = pathinfo($file, PATHINFO_EXTENSION);
		
		return Array(
			'content' => (in_array($extension, $extensions)) ? file_get_contents($file) : '',
			'mode' => $extension
		);
	}

	public function DeleteFile ($request)
	{
		$file = ROOTPATH . 'themes' . DS . $this->getParam() . DS . $request['file'];
		
		if (! (strpos($file, '..') === false && is_file($file))){
			throw new CoreException('File not "' . $file . '" found');
		}
		
		if (is_file($file)){
			@unlink($file);
		}
		
		return Array(
			'msg' => "Plik {$file} został skasowany."
		);
	}

	public function getHelpContent ($id)
	{
		$parts = array_reverse(explode('.', $id));
		$theme = array_pop($parts);
		$path = ROOTPATH . 'themes' . DS . $theme . DS . 'templates' . DS . implode(DS, array_reverse($parts)) . DS . 'instructions.html';
		return 'Zostanie w tym miejscu zaincludowany opis z pliku HTML, opisującego zastosowanie konkretnego pluginu, w tym przypadku:<br />' . $path;
	}
}