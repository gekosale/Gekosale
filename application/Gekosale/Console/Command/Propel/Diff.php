<?php

namespace Gekosale\Console\Command\Propel;
use Gekosale\Console\Command\AbstractCommand;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Diff extends AbstractCommand
{

    protected function configure ()
    {
        $this->setName('propel:diff');
        
        $this->setDescription('Generate Propel diff files');
        
        $this->setHelp(sprintf('%Generate Propel diff files.%s', PHP_EOL, PHP_EOL));
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $propelBin = ROOTPATH . 'vendor' . DS . 'propel' . DS . 'propel' . DS . 'bin' . DS . 'propel';
        
        $inputDir = ROOTPATH . 'sql';
        
        $outputDir = ROOTPATH;
        
        system("php {$propelBin} migration:diff --input-dir {$inputDir} --output-dir {$outputDir}/sql");
    }
}