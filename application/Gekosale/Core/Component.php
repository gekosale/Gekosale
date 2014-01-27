<?php

namespace Gekosale\Core;
use Symfony\Component\DependencyInjection\ContainerAware;

class Component extends ContainerAware
{

    public function has ($id)
    {
        return $this->container->has($id);
    }

    public function get ($id)
    {
        return $this->container->get($id);
    }

    public function getParam ($param = 'param', $default = null)
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

    public function getForm ($id)
    {
        return $this->container->get('form.resolver')->getForm($id);
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
        return $this->container->get('request');
    }

    protected function getRegistry ()
    {
        return $this->registry;
    }

    protected function getComponent ($id)
    {
        return $this->container->get('component.resolver')->getComponent($id);
    }

    protected function getModel ($id)
    {
        return $this->container->get('model.resolver')->getModel($id);
    }

    protected function getLocale ($id)
    {
        $languages = $this->container->getParameter('languages');
        return $languages[$id];
    }
}
