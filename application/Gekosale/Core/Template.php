<?php

namespace Gekosale\Core;

use Twig_Environment,
    Twig_Loader_Filesystem,
    Twig_Loader_String,
    Twig_Filter_Function,
    Twig_Function_Function,
    Twig_Extension_Optimizer,
    Twig_NodeVisitor_Optimizer;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Template
{

    protected $container;

    protected $extensions;

    protected $mode;

    protected $loader;

    protected $options;

    protected $engine;

    public function __construct(ContainerInterface $container, Twig_Loader_Filesystem $loader, $options)
    {
        $this->container  = $container;
        $this->extensions = $this->container->findTaggedServiceIds('twig.extension');
        $this->mode       = $this->container->get('request')->attributes->get('mode');
        $this->loader     = $loader;
        $this->options    = $options;
    }

    public function initEngine()
    {
        $this->engine = new Twig_Environment($this->loader, $this->options);
        foreach ($this->extensions as $extension) {
            $this->engine->addExtension($this->container->get($extension));
        }
    }
}