<?php

/** This file is part of KCFinder project
  *
  *      @desc Base configuration file
  *   @package KCFinder
  *   @version 2.51
  *    @author Pavel Tzonkov <pavelc@users.sourceforge.net>
  * @copyright 2010, 2011 KCFinder Project
  *   @license http://www.opensource.org/licenses/gpl-2.0.php GPLv2
  *   @license http://www.opensource.org/licenses/lgpl-2.1.php LGPLv2
  *      @link http://kcfinder.sunhater.com
  */

// IMPORTANT!!! Do not remove uncommented settings in this file even if
// you are using session configuration.
// See http://kcfinder.sunhater.com/install for setting descriptions

$_CONFIG = array(
    
    'disabled' => false,
    'denyZipDownload' => true,
    'denyUpdateCheck' => true,
    'denyExtensionRename' => true,
    
    'theme' => "oxygen",
    
    'uploadURL' => "upload",
    'uploadURL' => '../../../_images_frontend/upload/',
    'uploadURL' => 'http://' . $_SERVER['HTTP_HOST'] . '/upload/',
    
    'dirPerms' => 0755,
    'filePerms' => 0644,
    
    'access' => array(
        
        'files' => array(
            'upload' => true,
            'delete' => true,
            'copy' => true,
            'move' => true,
            'rename' => true
        ),
        
        'dirs' => array(
            'create' => true,
            'delete' => true,
            'rename' => true
        )
    ),
    
    'deniedExts' => "exe com msi bat php phps phtml php3 php4 cgi pl",
    
    'types' => array(
        'flash' => "swf",
        'images' => "*img"
    ),
    
    'filenameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),
    
    'dirnameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),
    
    'mime_magic' => "",
    
    'maxImageWidth' => 0,
    'maxImageHeight' => 0,
    
    'thumbWidth' => 100,
    'thumbHeight' => 100,
    
    'thumbsDir' => ".thumbs",
    
    'jpegQuality' => 90,
    
    'cookieDomain' => "",
    'cookiePath' => "",
    'cookiePrefix' => 'KCFINDER_',
    
    '_check4htaccess' => true
);