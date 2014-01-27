<?php

namespace Gekosale\Core;

use Twig_Environment,
    Twig_Loader_Filesystem,
    Twig_Extension_Optimizer,
    Twig_NodeVisitor_Optimizer;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Assetic\Extension\Twig\AsseticExtension;
use Assetic\Factory\AssetFactory;
use Assetic\AssetWriter;
use Assetic\Extension\Twig\TwigFormulaLoader;
use Assetic\Extension\Twig\TwigResource;
use Assetic\Factory\LazyAssetManager;

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

        $this->engine->addExtension(new Twig_Extension_Optimizer(Twig_NodeVisitor_Optimizer::OPTIMIZE_ALL));

        $factory = new AssetFactory(ROOTPATH . 'design');
        $this->engine->addExtension(new AsseticExtension($factory));

//
//        $templates = Array(
//            'layout.twig'
//        );
//        $am = new LazyAssetManager($factory);
//        $am->setLoader('twig', new TwigFormulaLoader($this->engine));
//        foreach ($templates as $template) {
//            $resource = new TwigResource($this->loader, $template);
//            $am->addResource($resource, 'twig');
//        }
//        $writer = new AssetWriter(ROOTPATH);
//        $writer->writeManagerAssets($am);
    }

    public function getEngine()
    {
        return $this->engine;
    }
}