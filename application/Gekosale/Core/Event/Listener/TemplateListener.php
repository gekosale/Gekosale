<?php

namespace Gekosale\Core\Event\Listener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

class TemplateListener implements EventSubscriberInterface
{

    public function onKernelController(FilterControllerEvent $event)
    {
        $event->getRequest()->attributes->set('_template_vars', Array());
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $container        = $event->getDispatcher()->getContainer();
        $request          = $event->getRequest();
        $controller       = $request->attributes->get('controller');
        $action           = $request->attributes->get('action');
        $controllerResult = $event->getControllerResult();
        $templateVars     = $request->attributes->get('_template_vars');

        $parameters = array_merge($templateVars, $controllerResult);

        $controllerParts = explode('\\', $controller);

        $template = $this->guessTemplateName($controller, $action);

        switch ($request->attributes->get('mode')) {
            case 'admin':
                $response = $container->get('template.admin')->engine->render($template, $parameters);
                break;
            case 'frontend':
                $response = $container->get('template.front')->engine->render($template, $parameters);
                break;
        }

        $event->setResponse(new Response($response));
    }

    /**
     * Guesses and returns the template name to render based on the controller
     * and action names.
     *
     * @param  string $route
     * @param  string $action
     *
     * @return string    Template name
     */
    protected function guessTemplateName($controller, $action)
    {
        return sprintf('%s\%s.twig', $route, $action);
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