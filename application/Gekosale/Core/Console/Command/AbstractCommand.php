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
namespace Gekosale\Core\Console\Command;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Filesystem\Exception\IOException,
    Symfony\Component\Filesystem\Filesystem,
    Symfony\Component\Finder\Finder;

/**
 * Class AbstractCommand
 *
 * @package Gekosale\Core\Console\Command
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
abstract class AbstractCommand extends Command
{
    protected function getMigrationClassesPath()
    {
        return ROOTPATH . 'application' . DS . 'Gekosale' . DS . 'Core' . DS . 'Migration';
    }

    protected function getFilesystem()
    {
        return $this->getApplication()->getContainer()->get('filesystem');
    }

    protected function getFinder()
    {
        return $this->getApplication()->getContainer()->get('finder');
    }

    protected function getDatabaseManager()
    {
        return $this->getApplication()->getContainer()->get('database_manager');
    }

    protected function getMigrationObject($namespace, $path)
    {
        require_once $path;
        $pathInfo = pathinfo($path);
        $class    = $namespace . '\\Migrate\\' . $pathInfo['filename'];

        return new $class();
    }
}