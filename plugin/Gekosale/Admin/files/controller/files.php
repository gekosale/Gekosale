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
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: files.php 619 2011-12-19 21:09:00Z gekosale $ 
 */
namespace Gekosale;

use FormEngine;
use sfEvent;
use Exception;

class FilesController extends Component\Controller\Admin
{

	public function index ()
	{
		App::getModel('contextmenu')->add($this->trans('TXT_PRODUCTS'), $this->getRouter()->url('admin', 'product'));
		App::getModel('contextmenu')->add($this->trans('TXT_TEMPLATE_LIBRARY'), $this->getRouter()->url('admin', 'templateeditor'));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllFiles',
			App::getModel('files'),
			'getFilesForAjax'
		));
		$this->registry->xajax->registerFunction(array(
			'GetFilenameSuggestions',
			App::getModel('files'),
			'getFilenameForAjax'
		));
		$this->registry->xajax->registerFunction(array(
			'doDeleteFiles',
			App::getModel('files'),
			'doAJAXDeleteFiles'
		));
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->assign('datagrid_filter', App::getModel('files')->getDatagridFilterData());
		$this->registry->template->display($this->loadTemplate('index.tpl'));
	}

	public function add ()
	{
		$allowedExtensions = array(
			'csv',
			'xml',
			'png',
			'gif',
			'jpg',
			'jpeg',
			'pdf',
			'txt',
			'doc',
			'xls',
			'mpp',
			'pdf',
			'vsd',
			'ppt',
			'docx',
			'xlsx',
			'pptx',
			'tif',
			'zip',
			'tgz',
			'ico',
			'avi',
			'mov',
			'wmf',
			'mp4',
			'flv',
			'html'
		);
		
		$allowedFolders = array(
			'upload/',
			'upload/keys/',
			'upload/competitors/',
			'design/_images_common/icons/languages/',
			'design/_images_frontend/upload/',
			'design/_images_frontend/core/logos/',
			'design/_images_frontend/staticlogos/',
			'themes/'
		);
		
		$pageschemes = App::getModel('pagescheme')->getPageschemeAll();
		foreach ($pageschemes as $pagescheme){
			$allowedFolders[] = 'themes/' . $pagescheme['templatefolder'] . '/assets/img/';
		}
		
		try{
			ob_start();
			if (! isset($_FILES['Filedata'])){
				echo '';
			}
			else{
				$_FILES['Filedata']['name'] = strtolower($_FILES['Filedata']['name']);
				$path = base64_decode($this->registry->core->getParam());
				if (strlen($path) > 0){
					$ext = strtolower(substr(strrchr($_FILES['Filedata']['name'], '.'), 1));
					if (! in_array($ext, $allowedExtensions)){
						throw new Exception('Wrong extension given.');
					}
					else{
						if (in_array($path, $allowedFolders)){
							$this->AddToFilesystem($_FILES['Filedata'], $path);
						}
						else{
							throw new Exception('Wrong path given.');
						}
					}
				}
				else{
					$_FILES['Filedata']['name'] = str_replace('.jpeg', '.jpg', $_FILES['Filedata']['name']);
					$this->AddToGallery($_FILES['Filedata']);
				}
			}
		}
		catch (Exception $e){
			echo $e->getMessage();
		}
	}

	public function edit ()
	{
		// dodawanie zdjec
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'edit_file',
			'action' => '',
			'method' => 'post'
		));
		
		$photosPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'photos_pane',
			'label' => $this->trans('TXT_PHOTOS')
		)));
		
		$photosPane->AddChild(new FormEngine\Elements\Image(Array(
			'name' => 'photo',
			'label' => $this->trans('TXT_PHOTOS'),
			'repeat_min' => 0,
			'repeat_max' => FormEngine\FE::INFINITE,
			'upload_url' => App::getURLAdressWithAdminPane() . 'files/add'
		)));
		
		$form->AddFilter(new FormEngine\Filters\Trim());
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			App::redirect(__ADMINPANE__ . '/files');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	protected function AddToFilesystem ($file, $path)
	{
		$filepath = $path . $file['name'];
		if (file_exists($filepath)){
			$file['name'] = time() . '_' . $file['name'];
			$filepath = $path . $file['name'];
		}
		if (! move_uploaded_file($file['tmp_name'], $filepath)){
			throw new Exception('File upload unsuccessful.');
		}
		echo "response = {sFilename: '{$file['name']}'}";
		
		App::getContainer()->get('cache')->delete('files');
	}

	protected function AddToGallery ($file)
	{
		$id = App::getModel('gallery/gallery')->process($file, 1);
		if (! preg_match('/\.swf$/', $file['name'])){
			$image = App::getModel('gallery/gallery')->getSmallImageById($id);
		}
		else{
			$image = Array(
				'path' => '',
				'filename' => $file['name'],
				'filextensioname' => 'swf',
				'filetypename' => 'application/x-shockwave-flash'
			);
		}
		echo "response = {sId: '{$id}', sThumb: '{$image['path']}', sFilename: '{$image['filename']}', sExtension: '{$image['filextensioname']}', sFileType: '{$image['filetypename']}'}";
		die();
	}

	public function view ()
	{
		$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
		
		$file = str_replace('.jpeg', '.jpg', $_FILES['file']['name']);
		
		if ($_FILES['file']['type'] == 'image/png' || $_FILES['file']['type'] == 'image/jpg' || $_FILES['file']['type'] == 'image/gif' || $_FILES['file']['type'] == 'image/jpeg' || $_FILES['file']['type'] == 'image/pjpeg'){
			$dir = ROOTPATH . 'design' . DS . '_images_frontend' . DS . 'upload' . DS . 'images' . DS;
			if (! move_uploaded_file($_FILES['file']['tmp_name'], $dir . $file)){
				throw new Exception('File upload unsuccessful.');
			}
			$Data = array(
				'filedir' => $_FILES,
				'filelink' => DESIGNPATH . '_images_frontend/upload/images/' . $file
			);
		}
		
		echo json_encode($Data);
	}

	public function confirm ()
	{
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		$targetDir = ROOTPATH . 'themes' . DS . base64_decode($this->registry->core->getParam());
		$cleanupTargetDir = true; // Remove old files
		$maxFileAge = 5 * 3600; // Temp file age in seconds
		
		@set_time_limit(5 * 60);
		
		// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
		
		$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);
		
		if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)){
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);
			
			$count = 1;
			while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
				$count ++;
			
			$fileName = $fileName_a . '_' . $count . $fileName_b;
		}
		
		$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
		
		if (! file_exists($targetDir))
			@mkdir($targetDir);
		
		if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))){
			while (($file = readdir($dir)) !== false){
				$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
				
				if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")){
					@unlink($tmpfilePath);
				}
			}
			
			closedir($dir);
		}
		else
			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
			
			// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
		
		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];
		
		if (strpos($contentType, "multipart") !== false){
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])){
				$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
				if ($out){
					$in = fopen($_FILES['file']['tmp_name'], "rb");
					
					if ($in){
						while ($buff = fread($in, 4096))
							fwrite($out, $buff);
					}
					else
						die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
					fclose($in);
					fclose($out);
					@unlink($_FILES['file']['tmp_name']);
				}
				else
					die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			}
			else
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		}
		else{
			$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
			if ($out){
				$in = fopen("php://input", "rb");
				
				if ($in){
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				}
				else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
				
				fclose($in);
				fclose($out);
			}
			else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}
		
		if (! $chunks || $chunk == $chunks - 1){
			rename("{$filePath}.part", $filePath);
		}
		
		die('{"jsonrpc" : "2.0", "result" : \'' . $targetDir . '\', "id" : "id"}');
	}
}