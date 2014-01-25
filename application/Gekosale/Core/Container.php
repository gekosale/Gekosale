<?php

/**
 * WellCommerce
 *
 * @copyright   Copyright (c) 2012-2014 WellCommerce
 * @author      WellCommerce, info@wellcommerce.pl
 */
namespace Gekosale\Core;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class Container
{

    protected $container;

    protected $parameters;

    protected $loader;

    public function __construct()
    {
        $this->container = $this->getContainerBuilder();
        $this->settings  = $this->getSettings();
        $this->loader    = new XmlFileLoader($this->container, new FileLocator(ROOTPATH . 'config'));
        $this->loader->load('config.xml');
    }

    protected function getSettings()
    {
        return include_once(ROOTPATH . 'config' . DS . 'settings.php');
    }

    protected function getContainerBuilder()
    {
        return new ContainerBuilder(new ParameterBag($this->getKernelParameters()));
    }

    protected static function getKernelParameters()
    {
        return array(
            'application.root_path'                 => ROOTPATH,
            'session.client_data_encription_string' => self::$config['client_data_encription_string'],
            'session.session_gc_maxlifetime'        => isset(self::$config['session_gc_maxlifetime'])
                    ? self::$config['session_gc_maxlifetime'] : ini_get('session.gc_maxlifetime'),
            'database'                              => self::$config['database']
        );
    }
}