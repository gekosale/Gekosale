<?php

namespace Gekosale\Core\Extension\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Asset extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('asset', array($this, 'getAsset'),
                array(
                     'is_safe' => Array('html')
                )
            )
        );
    }

    public function getAsset($path)
    {
        return sprintf('%s/%s', $this->container->get('request')->getSchemeAndHttpHost(), $path);
    }

    public function getName()
    {
        return 'asset';
    }
}
