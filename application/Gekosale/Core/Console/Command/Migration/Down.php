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

    protected function configure ()
    {
        $this->setName('migration:down');
        
        $this->setDescription('Executes migration classes');
        
        $this->setHelp(sprintf('%Executes "down" command in migration classes.%s', PHP_EOL, PHP_EOL));
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute (InputInterface $input, OutputInterface $output)
    {
        print_r($this->getApplication());
        die();


    }
}