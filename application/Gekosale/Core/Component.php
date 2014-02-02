<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Core
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\PropertyAccess\PropertyAccess;

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

    protected function getRouter ()
    {
        return $this->container->get('router');
    }

    protected function getRequest ()
    {
        return $this->container->get('request');
    }

    protected function getModel ($class)
    {
        return $this->container->get('resolver.model')->create($class);
    }

    protected function getForm ($class)
    {
        return $this->container->get('resolver.form')->create($class);
    }

    protected function getDatagrid ()
    {
        return $this->container->get('datagrid');
    }

    protected function getLocales ()
    {
        return array_keys($this->container->getParameter('locales'));
    }

    public function getPropertyAccessor ()
    {
        return PropertyAccess::createPropertyAccessor();
    }

    public function registerXajaxMethod ($method, $model)
    {
        $this->container->get('xajax')->registerFunction(array(
            $method,
            $model,
            $method
        ));
    }

    public function getJavascript ()
    {
        return $this->container->get('xajax')->getJavascript();
    }
}
