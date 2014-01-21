<?php

namespace Gekosale\Console\Command\Admin;
use Gekosale\Console\Command\AbstractCommand;
use Gekosale\Db;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Add extends AbstractCommand
{

    protected function configure ()
    {
        $this->setName('admin:add');
        
        $this->setDescription('Add admin account');
        
        $this->setDefinition(array(
            new InputArgument('user', InputArgument::REQUIRED, 'Username'),
            new InputArgument('pass', InputArgument::REQUIRED, 'Password')
        ));
        
        $this->setHelp(sprintf('%sAdds an admin account.%s', PHP_EOL, PHP_EOL));
    }

    protected function interact (InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelper('dialog');
        
        $user = $dialog->ask($output, 'Please enter user name [pomoc@wellcommerce.pl]: ', 'pomoc@wellcommerce.pl');
        
        $input->setArgument('user', $user);
        
        $generatedPassword = \Gekosale\Core::passwordGenerate();
        
        $password = $dialog->ask($output, 'Please enter password [' . $generatedPassword . ']: ', $generatedPassword);
        
        $input->setArgument('pass', $password);
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $result = $input->getArguments();
        $userid = $this->addUser($result['user'], $result['pass']);
        $this->addUserData($result['user'], $userid);
        
        $out = sprintf('%sAdded an admin account for %s with password %s.%s', PHP_EOL, $result['user'], $result['pass'], PHP_EOL);
        
        $output->write($out);
    }

    protected function addUser ($email, $password, $active = 1)
    {
        $hash = new \PasswordHash\PasswordHash();
        
        $sql = 'INSERT INTO user SET
					login = :login,
					password = :password,
        			globaluser = :globaluser,
					active = :active';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('login', $hash->HashLogin($email));
        $stmt->bindValue('password', $hash->HashPassword($password));
        $stmt->bindValue('active', $active);
        $stmt->bindValue('globaluser', 1);
        $stmt->execute();
        $userId = Db::getInstance()->lastInsertId();
        
        $sql = 'INSERT INTO usergroup SET
					groupid = :groupid,
					userid = :userid';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('userid', $userId);
        $stmt->bindValue('groupid', 1);
        $stmt->execute();
        
        return $userId;
    }

    protected function addUserData ($email, $userId)
    {
        $sql = 'INSERT INTO userdata SET
					firstname = :firstname,
					surname = :surname,
					email = :email,
					userid = :userid,
					photoid = :photoid';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('firstname', 'WellCommerce');
        $stmt->bindValue('surname', 'Support');
        $stmt->bindValue('email', $email);
        $stmt->bindValue('userid', $userId);
        $stmt->bindValue('photoid', NULL);
        $stmt->execute();
        return true;
    }
}