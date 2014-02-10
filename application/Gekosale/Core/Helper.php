<?php

namespace Gekosale\Core;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Helper
{

    protected $container;

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getViewId ()
    {
        if ($this->container->get('request')->attributes->get('mode') == 0) {
            return $this->container->get('session')->get('viewid');
        }
        else {
            return $this->container->get('session')->getActiveViewId();
        }
    }

    public function getViewIds ()
    {
        if (self::getViewId() > 0) {
            return Array(
                self::getViewId()
            );
        }
        else {
            return array_merge(Array(
                0
            ), App::getModel('stores')->getViewForHelperAll());
        }
    }

    public function getViewIdsDefault ()
    {
        return App::getModel('stores')->getViewForHelperAll();
    }

    public function getViewIdsAsString ()
    {
        return implode(',', self::getViewIds());
    }

    public function getLanguageId ()
    {
        return $this->container->get('session')->get('languageid');
    }

    public function getEncryptionKey ()
    {
        return $this->container->get('session')->getActiveEncryptionKeyValue();
    }

    public function setViewId ($id)
    {
        if (App::getRegistry()->router->getMode() == 0) {
            return $this->container->get('session')->setActiveMainsideViewId($id);
        }
        else {
            return $this->container->get('session')->setActiveViewId($id);
        }
    }

    public function getStoreId ()
    {
        if (App::getRegistry()->router->getMode() == 0) {
            return $this->container->get('session')->getActiveMainsideStoreId();
        }
        else {
            return $this->container->get('session')->getActiveStoreId();
        }
    }

    public function setStoreId ($id)
    {
        if (App::getRegistry()->router->getMode() == 0) {
            return $this->container->get('session')->setActiveMainsideStoreId($id);
        }
        else {
            return $this->container->get('session')->setActiveStoreId($id);
        }
    }
}