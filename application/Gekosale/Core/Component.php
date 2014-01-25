<?php

namespace Gekosale\Core;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Component extends ContainerAware
{
    public function getParam($param = 'param', $default = null)
    {
        return $this->registry->router->getParamFromRoute($param, $default);
    }

    public function trans($id)
    {
        return $this->container->get('translation')->trans($id);
    }

    public function getDb()
    {
        return $this->container->get('db');
    }

    public function getSession()
    {
        return $this->container->get('session');
    }

    protected function getTemplate()
    {
        return $this->registry->template;
    }

    protected function getCore()
    {
        return $this->registry->core;
    }

    protected function getRouter()
    {
        return $this->registry->router;
    }

    protected function getRequest()
    {
        return App::getRequest();
    }

    protected function getRegistry()
    {
        return $this->registry;
    }
}
