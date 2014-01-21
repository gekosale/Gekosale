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

class RedactorController extends Component\Controller\Admin
{
	protected $allowedImageExtensions = array(
		'png',
		'gif',
		'jpg',
		'jpeg'
	);
	protected $allowedVideoExtensions = array(
		'avi',
		'mov',
		'wmf',
		'mp4',
		'flv'
	);

	public function index ()
	{
	}

	public function add ()
	{
		$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
		
		$file = str_replace('.jpeg', '.jpg', $_FILES['file']['name']);
		
		if ($_FILES['file']['type'] == 'image/png' || $_FILES['file']['type'] == 'image/jpg' || $_FILES['file']['type'] == 'image/gif' || $_FILES['file']['type'] == 'image/jpeg' || $_FILES['file']['type'] == 'image/pjpeg'){
			$dir = ROOTPATH . 'design' . DS . '_images_frontend' . DS . 'upload' . DS . 'images' . DS;
			if (! move_uploaded_file($_FILES['file']['tmp_name'], $dir . $file)){
				throw new Exception('File upload unsuccessful.');
			}
			$Data = array(
				'filelink' => '/design/_images_frontend/upload/images/' . $file
			);
		}
		
		echo json_encode($Data);
	}

	public function view ()
	{
		$path = ROOTPATH . 'design' . DS . '_images_frontend' . DS . 'upload' . DS . 'images' . DS;
		$files = Array();
		$dirs = Array();
		if (($dir = opendir($path)) === false){
			throw new Exception('Directory "' + $path + '" cannot be listed.');
		}
		while (($file = readdir($dir)) !== false){
			if ($file == '.' || $file == '..'){
				continue;
			}
			$filepath = $path . $file;
			if (! is_dir($filepath)){
				
				if (in_array(pathinfo($path.$file, PATHINFO_EXTENSION), $this->allowedImageExtensions)){
					$files[] = Array(
						'image' => DESIGNPATH.'_images_frontend/upload/images/'.$file,
						'thumb' => DESIGNPATH.'_images_frontend/upload/images/'.$file
					);
				}
			}
		}
		closedir($dir);
		
		echo json_encode($files);
	}
}