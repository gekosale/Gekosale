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
 * $Id: virtualproduct.php 438 2011-08-27 09:29:36Z gekosale $
 */

namespace Gekosale;
use FormEngine;

class VirtualProductController extends Component\Controller\Admin
{

	public function index ()
	{
		return false;
	}

	public function add ()
	{
		
		try{
			ob_start();
			$_FILES['Filedata']['name'] = strtolower($_FILES['Filedata']['name']);
			$_FILES['Filedata']['name'] = str_replace('.jpeg', '.jpg', $_FILES['Filedata']['name']);
			App::getModel('virtualproductfiles')->process($_FILES['Filedata']);
			$id = App::getModel('virtualproductfiles')->insert($_FILES['Filedata']);
			$image = Array(
				'path' => '',
				'filename' => $_FILES['Filedata']['name'],
				'filextensioname' => App::getModel('virtualproductfiles')->getFileExtension($_FILES['Filedata']['name']),
				'filetypename' => $_FILES['Filedata']['type']
			);
			echo "response = {sId: '{$id}', sThumb: '{$image['path']}', sFilename: '{$image['filename']}', sExtension: '{$image['filextensioname']}', sFileType: '{$image['filetypename']}'}";
			die();
		}
		catch (Exception $e){
			echo $e->getMessage();
		}
	}

	public function edit ()
	{
		return false;
	}

	public function view ()
	{
		return false;
	}
}