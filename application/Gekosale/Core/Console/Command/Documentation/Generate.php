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
namespace Gekosale\Core\Console\Command\Documentation;

use Gekosale\Core\Console\Command\AbstractCommand;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sami\Sami;

/**
 * Class Generate
 *
 * @package Gekosale\Core\Console\Command\Documentation
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Generate extends AbstractCommand
{

    protected function configure()
    {
        $this->setName('documentation:generate');

        $this->setDescription('Generates documentation');

        $this->setHelp(sprintf('%Generates documentation.%s', PHP_EOL, PHP_EOL));
    }

    /**
     * Executes documentation:generate command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $iterator = $this->getFinder()->create()
            ->files()
            ->name('*.php')
            ->exclude('Resources')
            ->exclude('Tests')
            ->in(ROOTPATH . 'application');

        return new Sami($iterator, [
            'theme'                => 'symfony',
            'title'                => 'Gekosale API',
            'build_dir'            => ROOTPATH . 'docs' . DS . 'build',
            'cache_dir'            => ROOTPATH . 'docs' . DS . 'cache',
            'default_opened_level' => 2,
        ]);
    }
}