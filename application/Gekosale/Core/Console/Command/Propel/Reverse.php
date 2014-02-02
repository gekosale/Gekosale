<?php

namespace Gekosale\Core\Console\Command\Propel;

use Gekosale\Core\Console\Command\AbstractCommand;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Reverse extends AbstractCommand
{

    protected function configure ()
    {
        $this->setName('propel:reverse');
        
        $this->setDescription('Generate Propel schema files from existing database');
        
        $this->setHelp(sprintf('%Generate Propel schema files from existing database.%s', PHP_EOL, PHP_EOL));
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $propelBin = ROOTPATH . 'vendor' . DS . 'propel' . DS . 'propel' . DS . 'bin' . DS . 'propel';
        
        $inputDir = ROOTPATH . 'sql';
        
        $outputDir = ROOTPATH;
        
        system("php {$propelBin} reverse --input-dir {$inputDir} --output-dir {$outputDir}/sql mysql:host=localhost;dbname=gekosale3;user=root;password=");
    }
}