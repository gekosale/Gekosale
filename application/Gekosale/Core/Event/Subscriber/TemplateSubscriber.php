<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * @category    Gekosale
 * @package     Gekosale\Event\Listener
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Event\Subscriber;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

class TemplateSubscriber implements EventSubscriberInterface
{

    protected $engine = 'twig';

    public function onKernelController (FilterControllerEvent $event)
    {
        $event->getRequest()->attributes->set('_template_vars', Array());
    }

    public function onKernelView (GetResponseForControllerResultEvent $event)
    {
        $container = $event->getDispatcher()->getContainer();
        $request = $event->getRequest();
        $controller = $request->attributes->get('controller');
        $action = $request->attributes->get('action');
        $controllerResult = $event->getControllerResult();
        $templateVars = $request->attributes->get('_template_vars');
        
        /*
         * Always register Xajax
         */
        $templateVars['xajax'] = $container->get('xajax')->getJavascript();
        $container->get('xajax')->processRequest();
        
        $parameters = array_merge($templateVars, $controllerResult);
        
        switch ($request->attributes->get('mode')) {
            case 'admin':
                $template = $this->guessAdminTemplateName($controller, $action);
                $response = $container->get('template.admin')->engine->render($template, $parameters);
                break;
            case 'frontend':
                $template = $this->guessFrontTemplateName($controller, $action);
                $response = $container->get('template.front')->engine->render($template, $parameters);
                break;
        }
        
        $event->setResponse(new Response($response));
    }

    protected function guessAdminTemplateName ($controller, $action)
    {
        if (! preg_match('/Controller\\\Admin\\\(.+)$/', $controller, $matchController)) {
            throw new \InvalidArgumentException(sprintf('The "%s" class does not look like an admin controller class', $controller));
        }
        
        return sprintf('%s\%s.%s', strtolower($matchController[1]), $action, $this->engine);
    }

    protected function guessFrontTemplateName ($controller, $action)
    {
        if (! preg_match('/Controller\\\Frontend\\\(.+)$/', $controller, $matchController)) {
            throw new \InvalidArgumentException(sprintf('The "%s" class does not look like a frontend controller class', $controller));
        }
        
        return sprintf('%s\%s.%s', strtolower($matchController[1]), $action, $this->engine);
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