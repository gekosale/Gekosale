<?php
/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 627 $
 * $Author: gekosale $
 * $Date: 2012-01-20 23:05:57 +0100 (Pt, 20 sty 2012) $
 * $Id: index.php 627 2012-01-20 22:05:57Z gekosale $ 
 */

ini_set('display_errors', false);
(defined('E_DEPRECATED')) ? error_reporting(E_ALL & ~ E_DEPRECATED) : error_reporting(E_ALL);
DEFINE('__ENABLE_DEBUG__', 1);
DEFINE('DS', DIRECTORY_SEPARATOR);
DEFINE('ROOTPATH', dirname(__FILE__) . DS);
DEFINE('__PHPMAILER_CLASS__', ROOTPATH . 'lib' . DS . 'phpmailer' . DS);
DEFINE('__IMAGE_CLASS__', ROOTPATH . 'lib' . DS . 'imageGD' . DS);
DEFINE('__DISPATCHER_CLASS__', ROOTPATH . 'lib' . DS . 'dispatcher' . DS);
set_include_path(ROOTPATH . 'lib' . DS . PATH_SEPARATOR . get_include_path());
include_once (ROOTPATH . 'lib' . DS . 'xajax' . DS . 'xajax_core' . DS . 'xajax.inc.php');
date_default_timezone_set('Europe/Warsaw');
require_once ROOTPATH . 'lib' . DS . 'Symfony' . DS . 'Component' . DS . 'ClassLoader' . DS . 'UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;
$loader = new UniversalClassLoader();
$loader->register();
$loader->registerNamespaces(array(
    'Symfony\\Component\\HttpFoundation' => ROOTPATH . 'lib',
    'Symfony\\Component\\Routing' => ROOTPATH . 'lib',
    'Gekosale' => ROOTPATH . 'application',
    'FormEngine' => ROOTPATH . 'lib',
    'SimpleForm' => ROOTPATH . 'lib',
    'Doctrine' => ROOTPATH . 'lib',
    'PasswordHash' => ROOTPATH . 'lib'
));

require_once ROOTPATH . 'lib' . DS . 'Shopgate' . DS . 'shopgate.php';
require_once ROOTPATH . 'lib' . DS . 'Shopgate' . DS . 'Connector.php';
require_once ROOTPATH . 'lib' . DS . 'Shopgate' . DS . 'Config.php';

Gekosale\Autoloader::register();
Gekosale\App::init();
Gekosale\App::getRegistry()->router->setVariables();

DEFINE('DESIGNPATH', Gekosale\App::getURLForDesignDirectory());
 
$plugin = new WellCommerceShopgatePlugin();
$response = $plugin->handleRequest($_POST);
