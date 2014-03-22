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
namespace Gekosale\Plugin\ShippingMethod\Extension;

use Gekosale\Plugin\ShippingMethod\Extension\Compiler\RegisterShippingCalculatorsPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\HttpKernel\DependencyInjection\Extension,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ShippingMethodExtension
 *
 * @package Gekosale\Plugin\ShippingMethod\Extension
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ShippingMethodExtension extends Extension
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
        return 'gekosale.plugin.shipping_method';
    }
}