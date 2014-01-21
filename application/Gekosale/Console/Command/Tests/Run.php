<?php

namespace Gekosale\Console\Command\Tests;
use Gekosale\Console\Command\AbstractCommand;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends AbstractCommand
{

    protected function configure ()
    {
        $this->setName('tests:run');
        
        $this->setDescription('Test application');
        
        $this->setHelp(sprintf('%sTest application.%s', PHP_EOL, PHP_EOL));
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $phpUnitBin = ROOTPATH . 'vendor' . DS . 'bin' . DS . 'phpunit';
        
        system("{$phpUnitBin} -c config/phpunit.xml application/Gekosale/Tests/Controller/");
    }
}