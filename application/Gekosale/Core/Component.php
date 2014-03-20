<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace Gekosale\Core;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class Component
 *
 * Provides common methods needed in controllers, models and repositories
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
abstract class Component extends ContainerAware
{

    /**
     * Forwards request to another controller
     *
     * @param        $controller
     * @param string $action
     * @param array  $path
     * @param array  $query
     *
     * @return mixed
     */
    public function forward($controller, $action = 'indexAction', array $path = array(), array $query = array())
    {
        $path['_controller'] = $controller;
        $path['_action']     = $action;
        $request             = $this->container->get('request_stack')->getCurrentRequest();
        $subRequest          = $request->duplicate($query, null, $path);

        return $this->container->get('kernel')->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * Generates relative or absolute url based on given route and parameters
     *
     * @param string $route
     * @param array  $parameters
     * @param string $referenceType
     *
     * @return string Generated url
     */
    public function generateUrl($route, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * Returns true if the service id is defined.
     *
     * @param string $id The service id
     *
     * @return bool true if the service id is defined, false otherwise
     */
    final protected function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * Gets a service by id.
     *
     * @param string $id The service id
     *
     * @return object Service
     */
    final protected function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * Translates a string using the translation service
     *
     * @param string $id Message to translate
     *
     * @return string The message
     */
    final protected function trans($id)
    {
        return $this->container->get('translation')->trans($id);
    }

    /**
     * Shortcut to return the database service
     *
     * @return object Database manager service
     */
    final protected function getDb()
    {
        return $this->container->get('database_manager');
    }

    /**
     * Shortcut to return the session service
     *
     * @return object Session service
     */
    final protected function getSession()
    {
        return $this->container->get('session');
    }

    /**
     * Shortcut to return the session flashbag
     *
     * @return object FlashBag from session service
     */
    final protected function getFlashBag()
    {
        return $this->container->get('session')->getFlashBag();
    }

    /**
     * Shortcut to return the router service
     *
     * @return object Router service
     */
    final protected function getRouter()
    {
        return $this->container->get('router');
    }

    /**
     * Shortcut to return the request service
     *
     * @return Request
     */
    final protected function getRequest()
    {
        return $this->container->get('request');
    }

    /**
     * Shortcut to return event dispatcher service
     *
     * @return object Event dispatcher service
     */
    final protected function getDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

    /**
     * Shortcut to get param from current route
     *
     * @param string $index
     *
     * @return mixed
     */
    final protected function getParam($index)
    {
        return $this->container->get('request')->attributes->get($index);
    }

    /**
     * Shortcut to get Xajax service
     *
     * @return object Xajax
     */
    final protected function getXajax()
    {
        return $this->container->get('xajax');
    }

    /**
     * Shortcut to get XajaxManager service
     *
     * @return object XajaxManager
     */
    final protected function getXajaxManager()
    {
        if (!$this->container->has('xajax_manager')) {
            throw new \LogicException('Method getXajaxManager requires Container to have xajax_manager service');
        }

        return $this->container->get('xajax_manager');
    }

    /**
     * Shortcut to get Helper service
     *
     * @return object Helper
     */
    final protected function getHelper()
    {
        return $this->container->get('helper');
    }

    /**
     * Shortcut to get PDO instance
     *
     * @return object PDO
     */
    final protected function getPdo()
    {
        if (!$this->container->has('database_manager')) {
            throw new \LogicException('Method getPdo requires Container to have database_manager service');
        }

        return $this->container->get('database_manager')->getConnection()->getPdo();
    }

    /**
     * Shortcut to get PropertyAccessor
     *
     * @return \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    final protected function getPropertyAccessor()
    {
        return PropertyAccess::createPropertyAccessor();
    }

    /**
     * Shortcut to get LayoutManager service
     *
     * @return object LayoutManager
     */
    final public function getLayoutManager()
    {
        return $this->container->get('layout_manager');
    }

    /**
     * Shortcut to get all available languages
     *
     * @return mixed
     * @throws \LogicException
     */
    final protected function getLanguages()
    {
        if (!$this->container->has('language.repository')) {
            throw new \LogicException('Method getLanguages requires Container to have language.repository service');
        }

        return $this->container->get('language.repository')->all()->toArray();
    }

    /**
     * Shortcut to get all available language ids
     *
     * @return mixed
     * @throws \LogicException
     */
    final protected function getLanguageIds()
    {
        $collection = $this->getLanguages();
        $data       = [];

        foreach ($collection as $item) {
            $data[] = $item['id'];
        }

        return $data;
    }

    /**
     * Shortcut to get all available shops
     *
     * @return mixed
     * @throws \LogicException
     */
    final protected function getShops()
    {
        if (!$this->container->has('shop.repository')) {
            throw new \LogicException('Method getShops requires Container to have shop.repository service');
        }

        return $this->container->get('shop.repository')->all();
    }

    /**
     * Returns current language id
     *
     * @return int
     */
    final protected function getCurrentLanguage()
    {
        return 2;
    }

    /**
     * Shortcut to get Cache Manager service
     *
     * @return object
     */
    final protected function getCache()
    {
        return $this->container->get('cache_manager');
    }

    /**
     * Shortcut to encrypt value using encryption service
     *
     * @param $value
     *
     * @return mixed
     */
    final protected function encrypt($value)
    {
        return $this->get('encryption')->encrypt($value);
    }

    /**
     * Shortcut to decrypt value using encryption service
     *
     * @param $value
     *
     * @return mixed
     */
    final protected function decrypt($value)
    {
        return $this->get('encryption')->decrypt($value);
    }
}
