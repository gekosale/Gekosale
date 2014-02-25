<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

DEFINE('DS', DIRECTORY_SEPARATOR);

DEFINE('ROOTPATH', dirname(__FILE__) . DS);

setlocale(LC_ALL, "en_EN");

date_default_timezone_set('Europe/London');

$loader = require ROOTPATH . 'vendor/autoload.php';

Symfony\Component\Debug\Debug::enable();