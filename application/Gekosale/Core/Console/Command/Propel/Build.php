<?php

namespace Gekosale\Core\Console\Command\Propel;

use Gekosale\Core\Console\Command\AbstractCommand;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Build extends AbstractCommand
{

    protected function configure ()
    {
        $this->setName('propel:build');
        
        $this->setDescription('Build Propel models');
        
        $this->setHelp(sprintf('%sBuild Propel models.%s', PHP_EOL, PHP_EOL));
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $propelBin = ROOTPATH . 'vendor' . DS . 'propel' . DS . 'propel' . DS . 'bin' . DS . 'propel';
        
        $inputDir = ROOTPATH . 'sql';
        
        $outputDir = ROOTPATH;
        
        system("php {$propelBin} model:build    --input-dir {$inputDir} --output-dir {$outputDir}/application");
    }
}