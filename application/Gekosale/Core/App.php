<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale. Zabronione jest usuwanie informacji o
 * licencji i autorach.
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
 * $Id: app.class.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Core;
use xajax;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionManagerSingle;

class App
{

    protected static $URI = Array();

    protected static $registry;

    protected static $request;

    protected static $config;

    protected static $container;

    public static function getModel ($index)
    {
        if (is_object(self::$registry->$index)){
            return self::$registry->$index;
        }
        if (strpos($index, 'Model') === false){
            $indexModel = $index . 'Model';
            if (is_object(self::$registry->$indexModel)){
                return self::$registry->$indexModel;
            }
        }
        try{
            return self::$container->get('model.resolver')->createModel($index);
        }
        // allegro
        catch (\SoapFault $e){
            throw new \Exception($e->getMessage());
        }
        catch (Exception $e){
            throw new CoreException($e->getMessage());
        }
    }

    public static function getFormModel ($index)
    {
        return self::$container->get('form.resolver')->getModel($index);
    }

    public static function redirect ($path = false)
    {
        if ($path == false){
            header('Location: ' . self::getURLAdress());
        }
        else{
            header('Location: ' . self::getURLAdress() . $path);
        }
        die();
    }

    public static function redirectSeo ($url)
    {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $url);
        header('Connection: Close');
        die();
    }

    public static function redirectUrl ($url)
    {
        header('Location: ' . $url);
        header('Connection: Close');
        die();
    }

    public static function setRequest ()
    {
        $_SERVER['REQUEST_URI'] = rtrim($_SERVER['REQUEST_URI'], '/');
        self::$request = Request::createFromGlobals();
    }

    public static function getRequest ()
    {
        return self::$request;
    }

    public static function setUrl ()
    {
        $server_protocol = explode('/', self::$request->server->get('SERVER_PROTOCOL'));
        self::$URI = Array(
            'protocol' => strtolower($server_protocol[0]),
            'host' => self::$request->server->get('HTTP_HOST'),
            'script' => self::$request->server->get('REQUEST_URI')
        );
    }

    public static function getHost ($setProtocol = null)
    {
        if (! isset(self::$URI['host'])){
            return 'cli';
        }
        
        $host = self::$URI['host'];
        if (substr($host, - 2) == '//'){
            $host = substr($host, 0, - 1);
        }
        if ($setProtocol !== null){
            return strtolower(self::$URI['protocol']) . '://' . $host;
        }
        
        return $host;
    }

    public static function getHttps ()
    {
        return self::$URI['protocol'];
    }

    public static function getURLAdress ()
    {
        return App::getHost(1) . '/';
    }

    public static function getCurrentURLAdress ()
    {
        return (isset($_SERVER['SCRIPT_URI'])) ? $_SERVER['SCRIPT_URI'] : $_SERVER['PHP_SELF'];
    }

    public static function getURLAdressWithAdminPane ()
    {
        return self::getURLAdress() . App::getAdminPaneName();
    }

    public static function getURLForDesignDirectory ()
    {
        return App::getHost(1) . '/design/';
    }

    public static function getURLForAssetDirectory ()
    {
        $host = App::getHost();
        
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on'){
            $protocol = 'https://';
        }
        else{
            $protocol = 'http://';
        }
        
        return $protocol . $host . '/themes/';
    }

    public static function getURL ()
    {
        return strtolower(self::$URI['protocol']) . '://' . self::$URI['host'] . self::$URI['script'];
    }

    public static function getUri ()
    {
        return self::$URI;
    }

    public static function getConfig ($node = '')
    {
        if ('' === $node){
            return self::$config;
        }
        return isset(self::$config[$node]) ? self::$config[$node] : self::$config;
    }

    public static function init ()
    {
        self::$registry = new Registry();
        
        self::$config = include_once ROOTPATH . 'config' . DS . 'settings.php';
        
        App::setRequest();
        DEFINE('SSLNAME', (isset(self::$config['ssl']) && self::$config['ssl'] == 1) ? 'https' : 'http');
        DEFINE('__ADMINPANE__', self::$config['admin_panel_link']);
        App::setUrl();
        
        self::$container = self::getContainerBuilder();
        
        $loader = new XmlFileLoader(self::$container, new FileLocator(ROOTPATH . 'config'));
        
        $loader->load('config.xml');
        
        self::$registry->router = new Router(self::$registry, self::$container);
        self::$registry->loader = new Loader(self::$registry, self::$container);
        self::$registry->core = new Core(self::$registry, self::$container);
        self::$container->get('session')->setActiveEncryptionKeyValue((string) self::$config['client_data_encription_string']);
    }

    public static function getContainerBuilder ()
    {
        return new ContainerBuilder(new ParameterBag(self::getKernelParameters()));
    }

    public static function getContainer ()
    {
        return self::$container;
    }

    protected static function getKernelParameters ()
    {
        return array(
            'application.root_path' => ROOTPATH,
            'session.client_data_encription_string' => self::$config['client_data_encription_string'],
            'session.session_gc_maxlifetime' => isset(self::$config['session_gc_maxlifetime']) ? self::$config['session_gc_maxlifetime'] : ini_get('session.gc_maxlifetime'),
            'database' => self::$config['database']
        );
    }

    public static function Run ()
    {
        App::init();
        
        self::$registry->xajax = new xajax();
        
        if (self::$registry->router->getMode() == 0){
            $url = preg_replace_callback('~[^a-zA-Z0-9:/\?,_-]+~', function  ($s)
            {
                return urlencode($s[0]);
            }, self::$request->getUri());
            self::$registry->xajax->configure('requestURI', self::$registry->router->is404() ? self::getHost(1) : $url);
        }
        
        self::$registry->xajaxInterface = new XajaxInterface();
        
        self::$container->get('session')->clearTemp();
        
        DEFINE('URL', App::getHost(1) . '/');
        
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on'){
            DEFINE('DESIGNPATH', str_replace('http://', 'https://', App::getURLForDesignDirectory()));
        }
        else{
            DEFINE('DESIGNPATH', App::getURLForDesignDirectory());
        }
        
        self::$registry->template = new Template(self::$registry, self::$registry->router->getMode(), self::$container);
        
        self::$registry->template->setStaticTemplateVariables();
        
        $response = self::$container->get('kernel')->handle(self::$request);
        
        $response->send();
    }

    public static function getRegistry ()
    {
        return self::$registry;
    }

    public static function getAdminPaneName ()
    {
        return (self::$registry->router->getMode() != 1) ? '' : self::$registry->router->getAdminPaneName();
    }
}