<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * @category    Gekosale
 * @package     Gekosale
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */

DEFINE('DS', DIRECTORY_SEPARATOR);

DEFINE('ROOTPATH', dirname(__FILE__) . DS);

setlocale(LC_ALL, "pl_PL");
date_default_timezone_set('Europe/Warsaw');

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require ROOTPATH . 'vendor/autoload.php';

AnnotationRegistry::registerLoader(array(
    $loader,
    'loadClass'
));

Symfony\Component\Debug\Debug::enable();