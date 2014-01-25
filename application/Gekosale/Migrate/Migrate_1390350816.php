<?php

/**
*
* WellCommerce
*
* @copyright   Copyright (c) 2012-2014 WellCommerce
* @author      WellCommerce, info@wellcommerce.pl
*/
namespace Gekosale\Migrate;
use Gekosale\Core\Migrate;

class Migrate_1390350816 extends Migrate
{

    public function up ()
    {
        $files = $this->getFinder()->files()->in(ROOTPATH . 'application' . DS . 'Gekosale' . DS . 'Plugin' . DS . 'Frontend')->name('*.php')->contains('Component\Controller\Frontend');
        foreach ($files as $file){
            $content = $file->getContents();
            $path = $file->getRelativePath();
            $parts = explode(DS, $path);
            $parts[] = 'Frontend';
            $im = implode(DS, array_map('ucfirst', $parts));
            $newPath = ROOTPATH . 'application' . DS . 'Gekosale' . DS . 'Component' . DS . $im;
            $newFile = $newPath . DS . ucfirst($file->getFilename());
            
            $newNamespace = 'namespace Gekosale\\Component\\' . $im . ';';
            $newNamespace .= PHP_EOL . 'use Gekosale\Core\Component\Controller\Frontend;';
            $content = str_replace('namespace Gekosale\Plugin;', $newNamespace, $content);
            $content = str_replace('extends Component\Controller\Frontend', 'extends Frontend', $content);
            $content = str_replace('Controller extends Frontend', ' extends Frontend', $content);
            
            $this->getFilesystem()->copy($file->getRealpath(), $newFile, true);
            $this->getFilesystem()->dumpFile($newFile, $content);
        }
    }

    public function down ()
    {
    }
}