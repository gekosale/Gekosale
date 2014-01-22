<?php

/**
*
* WellCommerce
*
* @copyright   Copyright (c) 2012-2014 WellCommerce
* @author      WellCommerce, info@wellcommerce.pl
*/
namespace Gekosale\Core\Migrate;
use Gekosale\Core\Migrate;

class Migrate_1390136805 extends Migrate
{

    public function up ()
    {
        $files = $this->getFinder()->files()->in(ROOTPATH)->name('robots.txt')->notContains('Disallow: /regulamin');
        foreach ($files as $file){
            $content = $file->getContents();
            $content .= PHP_EOL . 'Disallow: /regulamin';
            $this->getFilesystem()->dumpFile($file->getRealpath(), $content);
        }
    }

    public function down ()
    {
    }
}