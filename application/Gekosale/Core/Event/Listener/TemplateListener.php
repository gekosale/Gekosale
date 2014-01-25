<?php

namespace Gekosale\Core\Event\Listener;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

class TemplateListener implements EventSubscriberInterface
{

    public function onKernelController (FilterControllerEvent $event)
    {
        $event->getRequest()->attributes->set('_template_vars', Array());
    }

    public function onKernelView (GetResponseForControllerResultEvent $event)
    {
        $container = $event->getDispatcher()->getContainer();
        $request = $event->getRequest();
        
        $controllerResult = $event->getControllerResult();
        $templateVars = $request->attributes->get('_template_vars');
        $templating = $container->get('template.admin');
        
        $parameters = array_merge($templateVars, $controllerResult);
        
        $template = $this->getTemplateName($request->attributes->get('controller'), $request->attributes->get('action'));
        
        $event->setResponse(new Response($container->get($templating)->render($template, $parameters)));
    }

    protected function getTemplateName ($controller, $action)
    {
        return sprintf('%s\%s', $controller, $action);
    }

    public static function getSubscribedEvents ()
    {
        return array(
            KernelEvents::CONTROLLER => array(
                'onKernelController',
                - 128
            ),
            KernelEvents::VIEW => 'onKernelView'
        );
    }
}