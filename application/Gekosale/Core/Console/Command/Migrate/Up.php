<?php

namespace Gekosale\Core\Console\Command\Migrate;
use Gekosale\Core\Console\Command\AbstractCommand;
use Gekosale\Core\Db;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Up extends AbstractCommand
{

    protected function configure ()
    {
        $this->setName('migrate:up');
        
        $this->setDescription('Run migrations');
        
        $this->setDefinition(array(
            new InputArgument('namespace', InputArgument::REQUIRED, 'Migrations namespace')
        ));
        
        $this->setHelp(sprintf('%Runs migrations.%s', PHP_EOL, PHP_EOL));
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
        $finder = $this->getFinder()->files()->in($path);
        
        foreach ($finder as $file){
            $migration = $this->getMigrationObject($namespace, $file->getRealpath());
            if ($migration->check() == 0){
                $migration->up();
                $migration->save();
                $output->writeln(sprintf('Executing migration "%s"', get_class($migration)));
            }
            else{
                $output->writeln(sprintf('Skipping migration "%s"', get_class($migration)));
            }
        }
        
        $output->writeln('Migration complete. No further migration to execute.');
    }
}