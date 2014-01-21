<?php

namespace Gekosale\Console\Command\Propel;
use Gekosale\Console\Command\AbstractCommand;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migration extends AbstractCommand
{

    protected function configure ()
    {
        $this->setName('propel:migration');
        
        $this->setDescription('Run Propel migrations');
        
        $this->setHelp(sprintf('%Run Propel migrations.%s', PHP_EOL, PHP_EOL));
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $propelBin = ROOTPATH . 'vendor' . DS . 'propel' . DS . 'propel' . DS . 'bin' . DS . 'propel';
        
        $inputDir = ROOTPATH . 'sql';
        
        $outputDir = ROOTPATH;
        
        system("php {$propelBin} migration:up --input-dir {$inputDir} --output-dir {$outputDir}/sql");
    }
}