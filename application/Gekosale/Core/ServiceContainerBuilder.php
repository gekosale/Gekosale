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

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterListenersPass;
use Gekosale\Core\DependencyInjection\Compiler\RegisterTwigExtensionsPass;
use Gekosale\Core\DependencyInjection\Extension\PluginExtensionLoader;

/**
 * Class ServiceContainerBuilder
 *
 * Manages cached Container
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
final class ServiceContainerBuilder
{

	/**
	 * Cached container class name
	 *
	 * @var string
	 */
	const SERVICE_CONTAINER_CLASS = 'ServiceContainer';

	/**
	 * Cached container parent class name
	 *
	 * @var string
	 */
	const SERVICE_CONTAINER_BASE_CLASS = 'Container';

	/**
	 * Container builder instance
	 *
	 * @var object
	 */
	protected $containerBuilder;

	/**
	 * Array containing default kernel parameters used in configuring services
	 *
	 * @var array
	 */
	protected $parameters;

	/**
	 * True if the debug mode is enabled, false otherwise
	 *
	 * @var bool
	 */
	protected $isDebug;

	/**
	 * Compiler passes registered to run during container build process
	 *
	 * @var array
	 */
	protected $compilerPasses;

	/**
	 * Path to cached container class
	 *
	 * @var string
	 */
	protected $compilerClassPath;

	/**
	 * Config instance class
	 *
	 * @var ConfigCache
	 */
	protected $containerConfigCache;

	/**
	 * Constructor
	 *
	 * @param array $parameters
	 * @param bool  $isDebug
	 */
	public function __construct(array $parameters, $isDebug = FALSE)
	{
		$this->parameters           = $parameters;
		$this->isDebug              = (bool)$isDebug;
		$this->compilerClassPath    = __DIR__ . DS . self::SERVICE_CONTAINER_CLASS . '.php';
		$this->containerConfigCache = new ConfigCache($this->compilerClassPath, $this->isDebug);
	}

	/**
	 * Checks if optimised container class needs to be regenerated
	 *
	 * @return void
	 */

	public function check()
	{
		if (!$this->containerConfigCache->isFresh()) {

			$this->initContainerBuilder();

			$this->loadXmlConfiguration();

			$this->registerExtensions();

			$this->registerCompilerPasses();

			foreach ($this->compilerPasses as $compilerPass) {
				$compilerPass->process($this->containerBuilder);
			}

			$this->compileAndSaveContainer();
		}
	}

	/**
	 * Register extensions using recursive scan
	 * 
	 * @return void
	 */
	protected function registerExtensions()
	{
		$extensionLoader = new PluginExtensionLoader($this->containerBuilder);
		$extensionLoader->registerExtensions();
	}

	/**
	 * Compiles container and saves it as an optimized class
	 *
	 * @return void
	 */
	protected function compileAndSaveContainer()
	{
		$this->containerBuilder->compile();

		$dumper = new PhpDumper($this->containerBuilder);

		$content = $dumper->dump(
		                  [
		                  'class'      => self::SERVICE_CONTAINER_CLASS,
		                  'base_class' => self::SERVICE_CONTAINER_BASE_CLASS,
		                  'namespace'  => __NAMESPACE__
		                  ]
		);

		$this->containerConfigCache->write($content, $this->containerBuilder->getResources());
	}

	/**
	 * Initializes Container Builder instance
	 *
	 * @return void
	 */
	protected function initContainerBuilder()
	{
		$parameterBag           = new ParameterBag($this->parameters);
		$this->containerBuilder = new ContainerBuilder($parameterBag);
	}

	/**
	 * Loads services configuration and predefined parameters from XML config file
	 *
	 * @return void
	 */
	protected function loadXmlConfiguration()
	{
		$locator = new FileLocator(ROOTPATH . 'config');

		$loader = new XmlFileLoader($this->containerBuilder, $locator);
		$loader->load('config.xml');
	}

	/**
	 * Registers Compiler passess used in process of compiling the container
	 *
	 * @param CompilerPassInterface $compilerPass
	 *
	 * @return void
	 */
	protected function registerCompilerPass(CompilerPassInterface $compilerPass)
	{
		$this->compilerPasses[] = $compilerPass;
	}

	/**
	 * Register all required compiler passes
	 *
	 * @return void
	 */
	protected function registerCompilerPasses()
	{
		/*
		 * RegisterListenersPass for all event listeners
		 */
		$this->registerCompilerPass(new RegisterListenersPass());

		/*
		 * RegisterTwigExtensionsPass for all services tagged with twig.extension
		 */
		$this->registerCompilerPass(new RegisterTwigExtensionsPass());
	}
}