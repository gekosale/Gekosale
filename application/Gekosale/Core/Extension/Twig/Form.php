<?php

namespace Gekosale\Core\Extension\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Form extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('form', array($this, 'Render'),
                array(
                     'is_safe' => Array('html')
                )
            )
        );
    }

    public function Render($form)
    {
        return $form->Render();
    }

    public function getName()
    {
        return 'form';
    }
}
