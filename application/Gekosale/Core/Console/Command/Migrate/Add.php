<?php

namespace Gekosale\Core\Console\Command\Migrate;

use Gekosale\Core\Console\Command\AbstractCommand;
use Gekosale\Core\Db;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Add extends AbstractCommand
{

    protected function configure ()
    {
        $this->setName('migrate:add');
        
        $this->setDescription('Adds new migration class');
        
        $this->setDefinition(array(
            new InputArgument('namespace', InputArgument::REQUIRED, 'Migration namespace')
        ));
        
        $this->setHelp(sprintf('%Adds new migration class.%s', PHP_EOL, PHP_EOL));
    }

    protected function interact (InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelper('dialog');
        
        $namespace = $dialog->ask($output, 'Please enter the namespace of the plugin [Gekosale]: ', 'Gekosale');
        
        $input->setArgument('namespace', $namespace);
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $args = $input->getArguments();
        $namespace = ucfirst($args['namespace']);
        $path = ROOTPATH . 'application' . DS . $namespace . DS . 'Migrate';
        $className = $this->getMigrationClassName();
        $fileContent = $this->getTemplate($namespace, $className);
        
        $this->createDirectory($path);
        $this->getFilesystem()->dumpFile($path . DS . $className . '.php', $fileContent);
        
        $out = sprintf('%sCreated new migration class %s.%s', PHP_EOL, $className, PHP_EOL);
        
        $output->write($out);
    }

    protected function getMigrationClassName ()
    {
        return 'Migrate_' . time();
    }

    protected function getTemplate ($namespace, $classname)
    {
        ob_start();
        ob_implicit_flush(0);
        include ROOTPATH . 'application' . DS . 'Gekosale' . DS . 'Core' . DS . 'Console' . DS . 'Template' . DS . 'Migrate.php';
        
        return ob_get_clean();
    }
}