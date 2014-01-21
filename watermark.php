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
 * $Revision: 527 $
 * $Author: gekosale $
 * $Date: 2011-09-12 08:44:11 +0200 (Pn, 12 wrz 2011) $
 * $Id: index.php 527 2011-09-12 06:44:11Z gekosale $ 
 */

DEFINE('__ENABLE_PROFILER__', 0);

if (__ENABLE_PROFILER__ == 1){
	$mtime = microtime();
	$mtime = explode(' ', $mtime);
	$mtime = $mtime[1] + $mtime[0];
	$starttime = $mtime;
}
$site_path = realpath(dirname(__FILE__));
ini_set('display_errors', true);

(defined('E_DEPRECATED')) ? error_reporting(E_ALL & ~ E_DEPRECATED) : error_reporting(E_ALL);

if (! defined('__SCRIPT_USE')){
	$__LOCAL_CATALOG = '';
	$__SERVER_DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
	if (substr($_SERVER['DOCUMENT_ROOT'], - 1) == '/'){
		$__SERVER_DOCUMENT_ROOT = substr($_SERVER['DOCUMENT_ROOT'], 0, - 1);
	}
	else{
		$__SERVER_DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
	}
	DEFINE('SERVER_DOCUMENT_ROOT', $__SERVER_DOCUMENT_ROOT);
	$__SCRIPT_FILENAME = str_replace($__SERVER_DOCUMENT_ROOT, '', $_SERVER['SCRIPT_FILENAME']);
	if (($indexPosition = strpos($__SCRIPT_FILENAME, '/index.php')) > 0){
		$requestURI = substr($_SERVER['REQUEST_URI'], $indexPosition);
		$__LOCAL_CATALOG = substr($_SERVER['REQUEST_URI'], 0, $indexPosition);
		if (strpos($__LOCAL_CATALOG, '/') == 0){
			$__LOCAL_CATALOG = substr($__LOCAL_CATALOG, 1);
		}
	}
	else{
		$requestURI = str_replace($__SCRIPT_FILENAME, '', $_SERVER['REQUEST_URI']);
	}
	if (! isset($_SERVER['SCRIPT_URI']) && strpos($requestURI, '/index.php') === FALSE && $requestURI != '/'){
		if (strpos($requestURI, '/') != 0){
			$requestURI = '/index.php/' . $requestURI;
		}
		else{
			$requestURI = '/index.php' . $requestURI;
		}
	}
	DEFINE('REQUEST_URI', $requestURI);
	if (strlen($__LOCAL_CATALOG) > 0){
		if (substr($__LOCAL_CATALOG, - 2) == '//'){
			$__LOCAL_CATALOG = substr($__LOCAL_CATALOG, 0, - 1);
		}
	}
	DEFINE('LOCAL_CATALOG', $__LOCAL_CATALOG);
}

DEFINE('__ENABLE_DEBUG__', 1);
DEFINE('DS', DIRECTORY_SEPARATOR);
DEFINE('ROOTPATH', dirname(__FILE__) . DS);
DEFINE('__CLASS_DIR__', ROOTPATH . 'application' . DS . 'gekosale' . DS);
DEFINE('__CREOLE_CLASS__', ROOTPATH . 'lib' . DS . 'creole' . DS);
DEFINE('__IMAGE_CLASS__', ROOTPATH . 'lib' . DS . 'imageGD' . DS);
set_include_path(ROOTPATH . 'lib' . DS . 'PEAR' . DS . PATH_SEPARATOR);
set_include_path(ROOTPATH . 'lib' . DS . PATH_SEPARATOR . get_include_path());
include_once (__CREOLE_CLASS__ . 'Creole.php');
date_default_timezone_set('Europe/Warsaw');

function autoLoader ($className)
{
	
	$directories = array(
		__CLASS_DIR__,
		__IMAGE_CLASS__
	);
	
	$fileNameFormats = array(
		'%s.class.php',
		'%s.php'
	);
	
	$path = $className;
	if (@include_once $path . '.php'){
		return;
	}
	
	$rootPathLen = strlen(ROOTPATH);
	
	foreach ($directories as $directory){
		foreach ($fileNameFormats as $fileNameFormat){
			$path = substr($directory, $rootPathLen) . sprintf($fileNameFormat, $className);
			if (is_file(ROOTPATH . strtolower($path))){
				include_once ROOTPATH . strtolower($path);
				return;
			}
			else 
				if (is_file(ROOTPATH . $path)){
					include_once ROOTPATH . $path;
					return;
				}
		}
	}
}

spl_autoload_register('autoLoader');
global $registry;

App::setUrl();
DEFINE('DESIGNPATH', App::getURLForDesignDirectory());
DEFINE('URL', App::getHost(1) . '/' . LOCAL_CATALOG);
$registry = new registry();
if (! @include_once (ROOTPATH . 'config' . DS . 'settings.php')){
	include (ROOTPATH . 'includes' . DS . 'install.php');
	die();
}
$registry->router = new Router($registry);
try{
	$registry->db = Db::getInstance($Config['database']);
}
catch (Exception $e){
	echo $e->getMessage();
	die();
}
$registry->session = new session($registry);
$registry->loader = new Loader($registry);
$registry->core = new Core($registry);
$layer = $registry->loader->getCurrentLayer();

$path = ROOTPATH . 'design' . DS . '_gallery' . DS . '_orginal' . DS . $_GET['image'];
if (is_file($path)){
	$objImage = new Image($path);
	if (isset($layer['watermark']) && ! is_null($layer['watermark']) && strlen($layer['watermark']) > 4){
		$watermark = new Image(ROOTPATH . 'design/_images_frontend/core/logos/' . $layer['watermark']);
		$objImage->watermark($watermark);
	}
	$objImage->display();
}