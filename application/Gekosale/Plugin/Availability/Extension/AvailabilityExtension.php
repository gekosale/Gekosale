<?php

namespace Gekosale\Plugin\Availability\Extension;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AvailabilityExtension
 *
 * @package Gekosale\Plugin\Availability\Extension
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class AvailabilityExtension extends Extension
{

	public function load(array $config, ContainerBuilder $container)
	{
		$loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../config'));
		$loader->load('services.xml');
	}

	public function getNamespace()
	{
		return 'http://symfony.com/schema/dic/services';
	}

	public function getAlias()
	{
		return 'gekosale.plugin.availability';
	}
}