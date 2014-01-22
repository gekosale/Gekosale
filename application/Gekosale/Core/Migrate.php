<?php

namespace Gekosale\Core;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

abstract class Migrate
{

    protected $filesystem;

    protected $finder;

    protected $container;

    protected $loader;

    protected $migrationClass;

    public function __construct ()
    {
        $this->settings = include (ROOTPATH . 'config' . DS . 'settings.php');
        $this->container = $this->getContainerBuilder();
        $this->loader = new XmlFileLoader($this->container, new FileLocator(ROOTPATH . 'config'));
        $this->loader->load('config.xml');
    }

    protected function getContainerBuilder ()
    {
        return new ContainerBuilder(new ParameterBag($this->getKernelParameters()));
    }

    protected function getKernelParameters ()
    {
        return array(
            'application.root_path' => ROOTPATH,
            'session.client_data_encription_string' => $this->settings['client_data_encription_string'],
            'database' => $this->settings['database']
        );
    }

    protected function getFilesystem ()
    {
        return $this->container->get('filesystem');
    }

    protected function getDb ()
    {
        return $this->container->get('db');
    }

    protected function getFinder ()
    {
        return $this->container->get('finder');
    }

    public function check ()
    {
        $this->migrationClass = get_class($this);
        
        $sql = 'SELECT COUNT(idmigration) AS total FROM migration WHERE migrationclass = :migrationclass';
        $stmt = $this->getDb()->getConnection()->prepare($sql);
        $stmt->bindValue('migrationclass', $this->migrationClass);
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs['total'];
    }

    public function save ()
    {
        $sql = 'INSERT INTO migration SET migrationclass = :migrationclass';
        $stmt = $this->getDb()->getConnection()->prepare($sql);
        $stmt->bindValue('migrationclass', $this->migrationClass);
        $stmt->execute();
    }

    abstract function up ();

    abstract function down ();
}