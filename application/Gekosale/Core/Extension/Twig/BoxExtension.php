<?php

namespace Gekosale\Core\Extension\Twig;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BoxExtension extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('box', array($this, 'getBoxContents'))
        );
    }

    public function getBoxContents($name, $parameters = array())
    {
        return $this->container->get('box.resolver')->getBoxContents($name, $parameters);
    }

    public function getName()
    {
        return 'box';
    }
}
