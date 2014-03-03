<?php
include 'bootstrap.php';

echo ROOTPATH;

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in(ROOTPATH . 'application');


return new Sami($iterator, array(
    'title'                => 'Gekosale API',
    'theme'                => 'enhanced',
    'build_dir'            => ROOTPATH . 'docs' . DS . 'build',
    'cache_dir'            => ROOTPATH . 'docs' . DS . 'cache',
    'default_opened_level' => 2,
));
