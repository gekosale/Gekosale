<?php

namespace Gekosale\Core;
use Gekosale\Core\EventDispatcher\Listener\TemplateListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;

class EventDispatcher extends ContainerAwareEventDispatcher
{

    protected $container;

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct($container);
    }

    public function addSubscribers ()
    {
        $this->addSubscriber(new RouterListener($this->container->get('router')->getMatcher()));
        $this->addSubscriber(new TemplateListener());
    }
}