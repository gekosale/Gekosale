<?php

namespace Gekosale\Core;

use Twig_Environment,
    Twig_Loader_Filesystem;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Template
{

    protected $container;

    protected $extensions;

    protected $loader;

    protected $options;

    public $engine;

    public function __construct(ContainerInterface $container, Twig_Loader_Filesystem $loader, $options)
    {
        $this->container  = $container;
        $this->extensions = $this->container->findTaggedServiceIds('twig.extension');
        $this->loader     = $loader;
        $this->options    = $options;
    }

    public function initEngine()
    {
        $this->engine = new Twig_Environment($this->loader, $this->options);
        foreach ($this->extensions as $extensionName => $extensionAttributes) {
            $this->engine->addExtension($this->container->get($extensionName));
        }
    }

    public function getEngine()
    {
        return $this->engine;
    }
}