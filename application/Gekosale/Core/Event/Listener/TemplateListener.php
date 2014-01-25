<?php

namespace Gekosale\Core\Event\Listener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

class TemplateListener implements EventSubscriberInterface
{

    const MODE_ADMIN = 1;
    const MODE_FRONT = 0;

    public function onKernelController(FilterControllerEvent $event)
    {
        $event->getRequest()->attributes->set('_template_vars', Array());
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $container        = $event->getDispatcher()->getContainer();
        $request          = $event->getRequest();
        $route            = $request->attributes->get('_route');
        $controllerResult = $event->getControllerResult();
        $templateVars     = $request->attributes->get('_template_vars');
        $templating       = $container->get('template.admin');

        $parameters = array_merge($templateVars, $controllerResult);

        $template = $this->getTemplateName(
            $route,
            $request->attributes->get('action')
        );

        switch ($request->attributes->get('mode')) {
            case self::MODE_ADMIN:
                $response = $container->get('template.front')->engine->render($template, $parameters);
                break;
            case self::MODE_FRONT:
                $response = $container->get('template.front')->engine->render($template, $parameters);
                break;
        }

        $event->setResponse(new Response($response));
    }

    protected function getTemplateName($controller, $action)
    {
        return sprintf('%s\%s.twig', $controller, $action);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(
                'onKernelController',
                -128
            ),
            KernelEvents::VIEW       => 'onKernelView'
        );
    }
}