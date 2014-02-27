<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the
 * Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it
 * through the
 * world-wide-web, please send an email to license@verison.pl so we can send you
 * a copy immediately.
 */
namespace FormEngine\Elements;

use Gekosale\App as App;

class ColourSchemePicker extends TextField
{

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
		$this->_attributes['session_name'] = session_name();
		$this->_attributes['session_id'] = session_id();
		$this->_attributes['file_types'] = Array(
			'jpg',
			'png',
			'gif',
			'swf'
		);
		if (! isset($this->_attributes['file_source'])){
			$this->_attributes['file_source'] = 'upload/';
		}
		$this->_attributes['file_types_description'] = \Gekosale\Translation::get('TXT_FILE_TYPES_IMAGE');
		$this->_attributes['upload_url'] = App::getURLAdressWithAdminPane() . 'files/add/' . base64_encode($this->_attributes['file_source']);
		$this->_attributes['load_handler'] = 'xajax_LoadFiles_' . $this->_id;
		App::getRegistry()->xajaxInterface->registerFunction(array(
			'LoadFiles_' . $this->_id,
			$this,
			'LoadFiles'
		));
		$this->_attributes['delete_handler'] = 'xajax_DeleteFile_' . $this->_id;
		App::getRegistry()->xajaxInterface->registerFunction(array(
			'DeleteFile_' . $this->_id,
			$this,
			'DeleteFile'
		));
		$this->_attributes['type_icons'] = Array(
			'cdup' => DESIGNPATH . '_images_panel/icons/filetypes/cdup.png',
			'unknown' => DESIGNPATH . '_images_panel/icons/filetypes/unknown.png',
			'directory' => DESIGNPATH . '_images_panel/icons/filetypes/directory.png',
			'gif' => DESIGNPATH . '_images_panel/icons/filetypes/image.png',
			'png' => DESIGNPATH . '_images_panel/icons/filetypes/image.png',
			'jpg' => DESIGNPATH . '_images_panel/icons/filetypes/image.png',
			'bmp' => DESIGNPATH . '_images_panel/icons/filetypes/image.png',
			'txt' => DESIGNPATH . '_images_panel/icons/filetypes/text.png',
			'doc' => DESIGNPATH . '_images_panel/icons/filetypes/text.png',
			'rtf' => DESIGNPATH . '_images_panel/icons/filetypes/text.png',
			'odt' => DESIGNPATH . '_images_panel/icons/filetypes/text.png',
			'htm' => DESIGNPATH . '_images_panel/icons/filetypes/document.png',
			'html' => DESIGNPATH . '_images_panel/icons/filetypes/document.png',
			'php' => DESIGNPATH . '_images_panel/icons/filetypes/document.png'
		);
	}

	protected function _PrepareAttributes_JS ()
	{
		$attributes = Array(
			$this->_FormatAttribute_JS('name', 'sName'),
			$this->_FormatAttribute_JS('label', 'sLabel'),
			$this->_FormatAttribute_JS('comment', 'sComment'),
			$this->_FormatAttribute_JS('error', 'sError'),
			$this->_FormatAttribute_JS('selector', 'sSelector'),
			$this->_FormatAttribute_JS('gradient_height', 'iGradientHeight'),
			$this->_FormatAttribute_JS('type_colour', 'bAllowColour', \FormEngine\FE::TYPE_BOOLEAN),
			$this->_FormatAttribute_JS('type_gradient', 'bAllowGradient', \FormEngine\FE::TYPE_BOOLEAN),
			$this->_FormatAttribute_JS('type_image', 'bAllowImage', \FormEngine\FE::TYPE_BOOLEAN),
			$this->_FormatAttribute_JS('file_source', 'sFilePath'),
			$this->_FormatAttribute_JS('upload_url', 'sUploadUrl'),
			$this->_FormatAttribute_JS('session_name', 'sSessionName'),
			$this->_FormatAttribute_JS('session_id', 'sSessionId'),
			$this->_FormatAttribute_JS('file_types', 'asFileTypes'),
			$this->_FormatAttribute_JS('type_icons', 'oTypeIcons', \FormEngine\FE::TYPE_OBJECT),
			$this->_FormatAttribute_JS('file_types_description', 'sFileTypesDescription'),
			$this->_FormatAttribute_JS('delete_handler', 'fDeleteFile', \FormEngine\FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('load_handler', 'fLoadFiles', \FormEngine\FE::TYPE_FUNCTION),
			$this->_FormatRules_JS(),
			$this->_FormatDependency_JS(),
			$this->_FormatDefaults_JS()
		);
		return $attributes;
	}

	public function DeleteFile ($request)
	{
		if (! isset($request['file'])){
			throw new Exception('No file specified.');
		}
		if (substr($request['file'], 0, strlen($this->_attributes['file_source'])) != $this->_attributes['file_source']){
			throw new Exception('The requested path "' . $request['file'] . '" is outside of permitted sandbox.');
		}
		if (! unlink($request['file'])){
			throw new Exception('Deletion of file "' . $request['file'] . '" unsuccessful.');
		}
		return Array();
	}

	public function LoadFiles ($request)
	{
		$inRoot = false;
		if (substr($request['path'], 0, strlen($this->_attributes['file_source'])) != $this->_attributes['file_source']){
			$request['path'] = $this->_attributes['file_source'];
		}
		if ($request['path'] == $this->_attributes['file_source']){
			$inRoot = true;
		}
		$path = ROOTPATH . $request['path'];
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
			$filepath = $path . $file;
			if (is_dir($filepath)){
				$dirs[] = Array(
					'dir' => true,
					'name' => $file,
					'path' => $request['path'] . $file,
					'size' => '',
					'owner' => '' . fileowner($filepath),
					'mtime' => date('Y-m-d H:i:s', filemtime($filepath))
				);
			}
			else{
				$files[] = Array(
					'dir' => false,
					'name' => $file,
					'path' => $request['path'] . $file,
					'size' => '' . filesize($filepath),
					'owner' => '' . fileowner($filepath),
					'mtime' => date('Y-m-d H:i:s', filemtime($filepath))
				);
			}
		}
		closedir($dir);
		return Array(
			'files' => array_merge($dirs, $files),
			'cwd' => $request['path']
		);
	}
}
