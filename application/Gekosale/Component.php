<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: controller.class.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Gekosale\Registry;
use Gekosale\App;

class Component extends ContainerAware
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Registry
     */
    protected $registry;

    public function __construct (Registry $registry = NULL, ContainerInterface $container = NULL)
    {
        if (NULL === $container){
            $container = App::getContainer();
        }
        $this->registry = $registry;
        $this->container = $container;
        $this->id = $this->registry->core->getParam();
        $this->model = App::getModel($this->getName());
        $this->formModel = App::getFormModel($this->getName());
    }

    public function getParam ($param = 'param', $default = NULL)
    {
        return $this->registry->router->getParamFromRoute($param, $default);
    }

    public function trans ($id)
    {
        return $this->container->get('translation')->trans($id);
    }

    public function getDb ()
    {
        return $this->container->get('db');
    }

    public function getSession ()
    {
        return $this->container->get('session');
    }

    protected function getTemplate ()
    {
        return $this->registry->template;
    }

    protected function getCore ()
    {
        return $this->registry->core;
    }

    protected function getRouter ()
    {
        return $this->registry->router;
    }

    protected function getRequest ()
    {
        return App::getRequest();
    }

    protected function getRegistry ()
    {
        return $this->registry;
    }
}
