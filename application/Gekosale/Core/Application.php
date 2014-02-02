<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Core
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Stopwatch\Stopwatch;
use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionManagerSingle;

class Application
{

    /**
     * Constructor
     */
    protected $container;

    protected $request;

    protected $response;

    protected $stopwatch;

    public function __construct ()
    {
        /*
         * Init Stopwatch component and start timing
         */
        $this->stopwatch = new Stopwatch();
        
        $this->stopwatch->start('application');
        
        /*
         * Get request
         */
        $this->request = Request::createFromGlobals();
        
        /*
         * Init Service Container
         */
        $this->container = $this->getContainerBuilder();
        
        /*
         * Load application configuration
         */
        $loader = new XmlFileLoader($this->container, new FileLocator(ROOTPATH . 'config'));
        $loader->load('config.xml');
        
        /*
         * Init Propel Connection Manager
         */
        $serviceContainer = Propel::getServiceContainer();
        $serviceContainer->setAdapterClass('default', 'mysql');
        $manager = new ConnectionManagerSingle();
        $manager->setConfiguration($this->container->getParameter('propel.config'));
        $serviceContainer->setConnectionManager('default', $manager);
        $this->container->set('propel.connection', Propel::getReadConnection('default')->getWrappedConnection());
        $this->container->set('urlgenerator', $this->container->get('router')->getGenerator());
    }

    public function run ()
    {
        /*
         * Resolve controller and dispatch application
         */
        $this->response = $this->container->get('kernel')->handle($this->request);
        $this->response->send();
    }

    public function stop ()
    {
        $this->container->get('kernel')->terminate($this->request, $this->response);
        $event = $this->stopwatch->stop('application');
        echo $event->getDuration();
    }

    protected function getContainerBuilder ()
    {
        return new ContainerBuilder(new ParameterBag($this->getKernelParameters()));
    }

    public function getContainer ()
    {
        return $this->container;
    }

    protected function getKernelParameters ()
    {
        return array(
            'application.root_path' => ROOTPATH
        );
    }
}