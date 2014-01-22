<?php

/**
 *
 * WellCommerce
 *
 * @copyright   Copyright (c) 2013 WellCommerce
 * @author      Adam Piotrowski, apiotrowski@wellcommerce.pl
 */
namespace Gekosale\Core\Console\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

abstract class AbstractCommand extends Command
{

    protected $filesystem;

    protected $finder;

    protected function getFilesystem ()
    {
        if (null === $this->filesystem){
            $this->filesystem = new Filesystem();
        }
        
        return $this->filesystem;
    }

    protected function getFinder ()
    {
        if (null === $this->finder){
            $this->finder = new Finder();
        }
        
        return $this->finder;
    }

    protected function createDirectory ($directory, $chmod = 0700)
    {
        $filesystem = $this->getFilesystem();
        
        try{
            $filesystem->mkdir($directory, $chmod);
        }
        catch (IOException $e){
            throw new IOException(sprintf('Unable to write the "%s" directory', $directory), 0, $e);
        }
    }

    protected function getMigrationObject ($namespace, $path)
    {
        require_once $path;
        $pathInfo = pathinfo($path);
        $class = $namespace . '\\Migrate\\' . $pathInfo['filename'];
        
        return new $class();
    }

    protected function getConfig ()
    {
        $config = include ROOTPATH . 'config' . DS . 'settings.php';
        return $config;
    }
}