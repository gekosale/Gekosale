<?php

namespace Gekosale\Core\Console\Command\Admin;

use Gekosale\Core\Console\Command\AbstractCommand;
use Gekosale\Core\Db;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Reset
 *
 * @package Gekosale\Core\Console\Command\Admin
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Reset extends AbstractCommand
{

    protected function configure ()
    {
        $this->setName('admin:reset');
        
        $this->setDescription('Resets an existing admin account');
        
        $this->setDefinition(array(
            new InputArgument('user', InputArgument::REQUIRED, 'User id'),
            new InputArgument('pass', InputArgument::REQUIRED, 'New password')
        ));
        
        $this->setHelp(sprintf('%sResets an existing admin account.%s', PHP_EOL, PHP_EOL));
    }

    protected function interact (InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelper('dialog');
        
        $users = $this->getUsers();
        
        $defaultUser = current($users);
        
        $question = Array();
        
        foreach ($this->getUsers() as $id => $user) {
            $question[] = "<comment>{$id}</comment>: {$user}\n";
        }
        
        $question[] = "\n<question>Choose an existing user [<comment>$defaultUser</comment>]:</question>\n";
        
        $user = $dialog->askAndValidate($output, $question, function  ($typeInput)
        {
            return $typeInput;
        }, 10, $defaultUser);
        
        $input->setArgument('user', $user);
        
        $generatedPassword = \Gekosale\Core::passwordGenerate();
        
        $password = $dialog->ask($output, 'Please enter new password [' . $generatedPassword . ']: ', $generatedPassword);
        
        $input->setArgument('pass', $password);
    }

    protected function getUsers ()
    {
        $sql = 'SELECT 
        			userid AS id, 
        			email
        		FROM userdata
				ORDER BY surname, firstname';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->execute();
        while ($rs = $stmt->fetch()) {
            $Data[$rs['id']] = $rs['email'];
        }
        return $Data;
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $result = $input->getArguments();
        
        $this->resetUser($result['user'], $result['pass']);
        
        $out = sprintf('%sAccount %s reseted with new password %s.%s', PHP_EOL, $result['user'], $result['pass'], PHP_EOL);
        
        $output->write($out);
    }

    protected function resetUser ($id, $password)
    {
        $hash = new \PasswordHash\PasswordHash();
        
        $sql = 'UPDATE user SET
					password = :password
        		WHERE iduser = :id';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('password', $hash->HashPassword($password));
        $stmt->bindValue('id', $id);
        $stmt->execute();
    }
}