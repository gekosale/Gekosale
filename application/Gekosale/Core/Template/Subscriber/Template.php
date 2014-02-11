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
namespace Gekosale\Core\Template\Subscriber;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Gekosale\Core\Template\Guesser\AdminTemplateGuesser;
use Gekosale\Core\Template\Guesser\FrontendTemplateGuesser;

class Template implements EventSubscriberInterface
{

    protected $engine = 'twig';

    public function onKernelController (FilterControllerEvent $event)
    {
        $event->getRequest()->attributes->set('_template_vars', Array());
    }

    public function onKernelView (GetResponseForControllerResultEvent $event)
    {
        /*
         * Fetch Service Container
         */
        $container = $event->getDispatcher()->getContainer();
        
        /*
         * Fetch Request object
         */
        $request = $event->getRequest();
        
        $controller = $request->attributes->get('controller');
        $action = $request->attributes->get('action');
        $controllerResult = $event->getControllerResult();
        $templateVars = $request->attributes->get('_template_vars');
        
        $parameters = array_merge($templateVars, $controllerResult);
        
        $guesser = $this->getGuesser($request->attributes->get('mode'));
        
        $template = $guesser->guess($controller, $action);
        
        $container->get($this->engine)->setLoader($container->get('twig.loader.admin'));
        
        $response = $container->get($this->engine)->render($template, $parameters);
        
        $event->setResponse(new Response($response));
    }

    protected function getGuesser ($mode)
    {
        return ('admin' === $mode) ? new AdminTemplateGuesser() : new FrontendTemplateGuesser();
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