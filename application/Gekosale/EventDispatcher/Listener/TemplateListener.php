<?php

namespace Gekosale\EventDispatcher\Listener;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

class TemplateListener implements EventSubscriberInterface
{

    public function onKernelView (GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $controllerResult = $event->getControllerResult();
        $event->setResponse(new Response($controllerResult));
    }

    public static function getSubscribedEvents ()
    {
        return array(
            KernelEvents::VIEW => 'onKernelView'
        );
    }
}