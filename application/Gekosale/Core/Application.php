<?php

/*
 * Gekosale Open-Source E-Commerce Platform
 * 
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 * 
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace Gekosale\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterListenersPass;
use Symfony\Component\Stopwatch\Stopwatch;
use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionManagerSingle;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Gekosale\Core\DependencyInjection\Extension\PluginExtensionLoader;
use Gekosale\Core\DependencyInjection\Compiler\RegisterTwigExtensionsPass;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Main application class.
 * 
 * Initializes containers, database connection, registers plugins and extensions.
 * 
 * Uses kernel dispatch and terminate events
 * 
 * @author Adam Piotrowski <adam@gekosale.com>
 */
class Application
{

    /**
     * Container instance
     * 
     * @var object
     */
    protected $container;

    /**
     * Container builder instance
     * 
     * @var object
     */
    protected $containerBuilder;

    /**
     * Request instance
     * 
     * @var object
     */
    protected $request;

    /**
     * Response instance
     * 
     * @var object
     */
    protected $response;

    /**
     * Stopwatch component instance
     * 
     * @var object
     */
    protected $stopwatch;

    /**
     * True if the debug mode is enabled, false otherwise
     * 
     * @var bool
     */
    protected $isDebug;

    /**
     * Constructor 
     * 
     * @param bool $isDebug Enable or disable debug mode in application
     */
    public function __construct ($isDebug)
    {
        $this->isDebug = $isDebug;
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
        $file = ROOTPATH . 'application' . DS . 'Gekosale' . DS . 'Core' . DS . 'ServiceContainer.php';
        
        $containerConfigCache = new ConfigCache($file, $this->isDebug);
        
        if (! $containerConfigCache->isFresh()) {
            
            $this->containerBuilder = new ContainerBuilder(new ParameterBag($this->getKernelParameters()));
            
            $loader = new XmlFileLoader($this->containerBuilder, new FileLocator(ROOTPATH . 'config'));
            $loader->load('config.xml');
            
            $this->initPropelContainer();
            
            $extensionLoader = new PluginExtensionLoader($this->containerBuilder);
            $extensionLoader->registerExtensions();
            
            $registerListenersPass = new RegisterListenersPass();
            $registerListenersPass->process($this->containerBuilder);
            
            $registerTwigExtensionsPass = new RegisterTwigExtensionsPass();
            $registerTwigExtensionsPass->process($this->containerBuilder);
            
            $this->containerBuilder->compile();
            
            $dumper = new PhpDumper($this->containerBuilder);
            
            $options = array(
                'class' => 'ServiceContainer',
                'base_class' => 'Container',
                'namespace' => __NAMESPACE__
            );
            
            $content = $dumper->dump($options);
            
            $containerConfigCache->write($content, $this->containerBuilder->getResources());
        }
        
        $this->container = new ServiceContainer();
    }

    /**
     * Inits Propel service container and registers new database connection
     *
     * @return  void
     */
    protected function initPropelContainer ()
    {
        $serviceContainer = Propel::getServiceContainer();
        $serviceContainer->setAdapterClass('default', 'mysql');
        $manager = new ConnectionManagerSingle();
        $manager->setConfiguration($this->containerBuilder->getParameter('propel.config'));
        $serviceContainer->setConnectionManager('default', $manager);
        
        $this->containerBuilder->set('propel.connection', Propel::getReadConnection('default')->getWrappedConnection());
    }

    /**
     * Resolves controller and dispatch the application
     *
     * @return  void
     */
    public function run ()
    {
        $this->response = $this->container->get('kernel')->handle($this->request);
        $this->response->send();
    }

    /**
     * Stops application and triggers termination events
     *
     * @return  void
     */
    public function stop ()
    {
        $this->container->get('kernel')->terminate($this->request, $this->response);
        $event = $this->stopwatch->stop('application');
        echo $event->getDuration();
    }

    /**
     * Returns all globally accessible kernel parameters
     *
     * @return  array
     */
    protected function getKernelParameters ()
    {
        return array(
            'application.root_path' => ROOTPATH,
            'application.debug_mode' => $this->isDebug
        );
    }
}