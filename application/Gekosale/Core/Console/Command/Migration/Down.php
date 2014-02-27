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
namespace Gekosale\Core\Console\Command\Migration;

use Gekosale\Core\Console\Command\AbstractCommand;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Down
 *
 * @package Gekosale\Core\Console\Command\Migration
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Down extends AbstractCommand
{

    protected function configure()
    {
        $this->setName('migration:down');

        $this->setDescription('Executes migration classes');

        $this->setHelp(sprintf('%Executes "down" command in migration classes.%s', PHP_EOL, PHP_EOL));
    }

    /**
     * Executes migration:down command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $this->getFinder()->files()->in($this->getMigrationClassesPath());

        foreach ($files as $file) {
            $migrationClass = '\\Gekosale\\Core\\Migration\\' . $file->getBasename('.php');
            $migrationObj   = new $migrationClass();
            $migrationObj->down();
        }
    }
}