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
 * $Id: cart.php 438 2011-08-27 09:29:36Z gekosale $
 */

namespace Gekosale\Plugin;

class CartController extends Component\Controller\Frontend
{

	public function index ()
	{
		$this->Render('Cart');
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
			'tgz'
		);
		
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		$targetDir = ROOTPATH . 'upload/order/';
		@set_time_limit(5 * 60);
		
		// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
		$chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
		
		$fileName = preg_replace('/[^\w\._]+/', '', $fileName);
		
		$filename = time() . '_' . $fileName;
		
		$ext = substr(strrchr($fileName, '.'), 1);
		if (! in_array($ext, $allowedExtensions)){
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Wrong extension given."}, "id" : "id"}');
		}
		
		if ($chunks < 2 && file_exists($targetDir . DS . $fileName)){
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);
			
			$count = 1;
			while (file_exists($targetDir . DS . $fileName_a . '_' . $count . $fileName_b))
				$count ++;
			
			$fileName = $fileName_a . '_' . $count . $fileName_b;
		}
		
		// Create target dir
		if (! file_exists($targetDir))
			@mkdir($targetDir);
		file_put_contents(ROOTPATH . 'logs/uploader.txt', json_encode($_FILES));
		if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])){
			$out = fopen($targetDir . DS . $fileName, $chunk == 0 ? "wb" : "ab");
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
				$Data = App::getContainer()->get('session')->getActiveOrderUploadedFiles();
				if (! isset($Data[$fileName])){
					$Data[$fileName] = $fileName;
					App::getContainer()->get('session')->setActiveOrderUploadedFiles($Data);
				}
				@unlink($_FILES['file']['tmp_name']);
			}
			else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}
		else
			die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		
		die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
	
	}
}