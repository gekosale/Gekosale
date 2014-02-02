<?php

namespace Gekosale\Core\Console\Command\Cache;

use Gekosale\Core\Console\Command\AbstractCommand;
use Gekosale\Core\Db;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegExIterator;
use FilesystemIterator;

class Clear extends AbstractCommand
{

    protected function configure ()
    {
        $this->setName('cache:clear');
        
        $this->setDescription('Clear all cache');
        
        $this->setHelp(sprintf('%Clear all cache.%s', PHP_EOL, PHP_EOL));
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $path = ROOTPATH . 'application' . DS . 'Gekosale' . DS . 'Component';
        
        $classmapPath = ROOTPATH . 'var' . DS . 'serialization' . DS . 'classesmap.reg';
        
        $dir = new RegExiterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::FOLLOW_SYMLINKS)), '~.+\.php\z~');
        
        $this->classesMap = array();
        $pathLen = strlen($path) + 1;
        foreach ($dir as $file) {
            if ($file->isFile() && ! preg_match('~migrate_\d+\z~', $file->getBasename('.php'))) {
                $this->classesMap[substr($file->getPathname(), $pathLen, - 4)] = $file->getPathname();
            }
        }
        
        file_put_contents($classmapPath, serialize($this->classesMap), LOCK_EX);
        
        $out = sprintf('%sCache cleared.', PHP_EOL);
        
        $output->write($out);
    }
}