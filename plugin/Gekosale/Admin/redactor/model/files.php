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
 * $Id: files.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;

class FilesModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('file', Array(
			'idfile' => Array(
				'source' => 'F.idfile'
			),
			'filename' => Array(
				'source' => 'F.name',
				'prepareForAutosuggest' => true
			),
			'fileextension' => Array(
				'source' => 'FE.name',
				'prepareForSelect' => true
			),
			'filetype' => Array(
				'source' => 'FT.name',
				'prepareForSelect' => true
			),
			'path' => Array(
				'source' => 'F.idfile',
				'processFunction' => Array(
					$this,
					'getOrginalPathForId'
				)
			),
			'adddate' => Array(
				'source' => 'F.adddate'
			),
			'thumb' => Array(
				'source' => 'F.idfile',
				'processFunction' => Array(
					$this,
					'getThumbPathForId'
				)
			)
		));
		$datagrid->setFrom('
				`file` F
				INNER JOIN `filetype` FT ON FT.idfiletype = F.filetypeid
				INNER JOIN `fileextension` FE ON FE.idfileextension = F.fileextensionid
				LEFT JOIN category CAT ON CAT.photoid = F.idfile
				LEFT JOIN productphoto PP ON PP.photoid = F.idfile
				LEFT JOIN userdata UD ON UD.photoid = F.idfile
				LEFT JOIN deliverer DEL ON DEL.photoid = F.idfile
				LEFT JOIN producer PRO ON PRO.photoid = F.idfile

				
			');
		$datagrid->setGroupBy('
				F.idfile
			');
	}

	public function getThumbPathForId ($id)
	{
		try{
			$image = App::getModel('gallery')->getSmallImageById($id);
		}
		catch (Exception $e){
			$image = Array(
				'path' => ''
			);
		}
		return $image['path'];
	}
	
	public function getOrginalPathForId ($id)
	{
		try{
			$image = App::getModel('gallery')->getOrginalImageById($id);
		}
		catch (Exception $e){
			$image = Array(
				'path' => ''
			);
		}
		return $image['path'];
	}

	public function getFilenameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('filename', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getFilesForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteFiles ($datagridName = 'list-files', $id = NULL)
	{
		try{
			return $this->getDatagrid()->deleteRow($datagridName, $id, Array(
				$this,
				'deleteFile'
			), $this->getName());
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function deleteFile ($Data)
	{
		if (! is_array($Data)){
			$Data = Array(
				$Data
			);
		}
		$fileData = Array();
		foreach ($Data as $fileid){
			$filesData[] = App::getModel('gallery')->getFileById($fileid);
		}
		
		foreach ($filesData as $file){
			DbTracker::deleteRows('file', 'idfile', $file['idfile']);
			App::getModel('gallery')->deleteFilesFromArray($file);
		}
		
		App::getContainer()->get('cache')->delete('files');
		App::getContainer()->get('cache')->delete('news');
		App::getContainer()->get('cache')->delete('contentcategory');
		App::getContainer()->get('cache')->delete('categories');
	
	}
}