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
namespace Gekosale\Plugin\ShippingMethod\Extension\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RegisterShippingCalculatorsPass
 *
 * @package Gekosale\Core\DependencyInjection\Compiler
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class RegisterShippingCalculatorsPass implements CompilerPassInterface
{

    /**
     * @param ContainerBuilder $container
     *
     * @see \Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface::process()
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('shipping_method.calculator')) {
            return;
        }

        $definition = $container->getDefinition('shipping_method.calculator');
        print_r($container->findTaggedServiceIds('shipping.calculator'));
        foreach ($container->findTaggedServiceIds('shipping.calculator') as $id => $attributes) {
            $class     = $container->getDefinition($id)->getClass();
            $refClass  = new \ReflectionClass($class);
            $interface = 'Gekosale\\Plugin\\ShippingMethod\\Calculator\\CalculatorInterface';
            if (!$refClass->implementsInterface($interface)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, $interface));
            }
            $definition->addMethodCall('addCalculator', array(
                $attributes[0]['alias'],
                new Reference($id)
            ));
        }
    }
}