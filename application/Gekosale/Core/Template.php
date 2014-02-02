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

use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_Extension_Optimizer;
use Twig_NodeVisitor_Optimizer;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Template
{

    public $engine;

    protected $container;

    protected $extensions;

    protected $loader;

    protected $options;

    public function __construct (ContainerInterface $container, Twig_Loader_Filesystem $loader, $options)
    {
        $this->container = $container;
        $this->extensions = $this->container->findTaggedServiceIds('twig.extension');
        $this->loader = $loader;
        $this->options = $options;
    }

    public function initEngine ()
    {
        $this->engine = new Twig_Environment($this->loader, $this->options);
        foreach ($this->extensions as $extensionName => $extensionAttributes) {
            $this->engine->addExtension($this->container->get($extensionName));
        }
        $this->engine->addExtension(new Twig_Extension_Optimizer(Twig_NodeVisitor_Optimizer::OPTIMIZE_ALL));
    }

    public function getEngine ()
    {
        return $this->engine;
    }
}