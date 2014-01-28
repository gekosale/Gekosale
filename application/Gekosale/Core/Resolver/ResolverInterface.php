<?php

namespace Gekosale\Core;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Resolver extends ContainerAware
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getNamespaces()
    {
        return $this->container->getParameter('application.namespaces');
    }
}