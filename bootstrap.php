<?php

DEFINE('__ENABLE_DEBUG__', 0);
DEFINE('DS', DIRECTORY_SEPARATOR);
DEFINE('ROOTPATH', dirname(__FILE__) . DS);

setlocale(LC_ALL, "pl_PL");
date_default_timezone_set('Europe/Warsaw');

$loader = require ROOTPATH . 'vendor/autoload.php';

Symfony\Component\Debug\Debug::enable();