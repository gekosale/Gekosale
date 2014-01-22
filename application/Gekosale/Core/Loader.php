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
 * $Id: loader.class.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Core;

class Loader
{

    const SYSTEM_NAMESPACE = 'Gekosale';

    protected $registry;

    protected $container;

    protected $events;

    protected $layer = Array();

    protected $namespace = 'core';

    protected $_viewid = 3;

    protected $fixedUrl = FALSE;

    public function __construct (&$registry, $container)
    {
        $this->registry = $registry;
        $this->container = $container;
        $this->loadView();
    }

    public function normalizeHost ($host)
    {
        $host = trim(strtolower($host));
        return (substr($host, 0, 4) == 'www.') ? substr($host, 4) : $host;
    }

    public function determineViewId ()
    {
        $sql = "SELECT viewid FROM viewurl WHERE url = :url";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('url', $this->normalizeHost(App::getHost()));
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            return $rs['viewid'];
        }
        
        $this->fixedUrl = TRUE;
        return $this->_viewid;
    }

    public function getViewUrl ()
    {
        if (Helper::getViewId() == 0){
            return App::getURLAdress();
        }
        
        $sql = "SELECT url FROM viewurl WHERE viewid = :viewid";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            return 'http://' . $rs['url'] . '/';
        }
    }

    protected function getScriptUri ()
    {
        $uri = App::getUri();
        
        return preg_replace_callback('~[^a-zA-Z0-9:;&/\?,_\-=\[\]]+~', function  ($s)
        {
            return urlencode($s[0]);
        }, $uri['script']);
        $script = str_replace('&', '&amp;', $script);
    }

    public function loadView ()
    {
        $sql = 'SELECT
					V.idview,
					V.name as shopname,
					V.namespace,
					C.idcurrency,
					C.currencysymbol,
					C.decimalseparator,
					C.decimalcount,
					C.thousandseparator,
					C.positivepreffix,
					C.positivesuffix,
					C.negativepreffix,
					C.negativesuffix,
					S.countryid,
					V.taxes,
					V.showtax,
					V.offline,
					gacode,
					gapages,
					gatransactions,
					googleappstag,
					cartredirect,
					photoid,
					favicon,
					forcelogin,
					apikey,
					watermark,
					confirmregistration,
					enableregistration,
					invoicenumerationkind,
					uploaderenabled,
					uploadmaxfilesize,
					uploadchunksize,
					uploadextensions,
					sendingo,
					V.pageschemeid,
					V.contactid,
					PS.templatefolder,
					wwwredirection
				FROM view V
				LEFT JOIN viewcategory VC ON VC.viewid = V.idview
				LEFT JOIN store S ON V.storeid = S.idstore
				LEFT JOIN pagescheme PS ON PS.idpagescheme = V.pageschemeid
				LEFT JOIN currency C ON C.idcurrency = IF(:currencyid > 0, :currencyid, V.currencyid)
				WHERE V.idview = :viewid';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('viewid', $this->determineViewId());
        $stmt->bindValue('currencyid', $this->container->get('session')->getActiveCurrencyId());
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            $this->layer = Array(
                'idview' => $rs['idview'],
                'namespace' => $rs['namespace'],
                'cartredirect' => $rs['cartredirect'],
                'gacode' => $rs['gacode'],
                'gapages' => $rs['gapages'],
                'gatransactions' => $rs['gatransactions'],
                'googleappstag' => $rs['googleappstag'],
                'offline' => $rs['offline'],
                'taxes' => $rs['taxes'],
                'showtax' => $rs['showtax'],
                'shopname' => $rs['shopname'],
                'photoid' => $rs['photoid'],
                'favicon' => $rs['favicon'],
                'watermark' => $rs['watermark'],
                'idcurrency' => $rs['idcurrency'],
                'currencysymbol' => $rs['currencysymbol'],
                'decimalseparator' => $rs['decimalseparator'],
                'decimalcount' => $rs['decimalcount'],
                'thousandseparator' => $rs['thousandseparator'],
                'positivepreffix' => $rs['positivepreffix'],
                'positivesuffix' => $rs['positivesuffix'],
                'negativepreffix' => $rs['negativepreffix'],
                'negativesuffix' => $rs['negativesuffix'],
                'countryid' => $rs['countryid'],
                'forcelogin' => $rs['forcelogin'],
                'confirmregistration' => $rs['confirmregistration'],
                'enableregistration' => $rs['enableregistration'],
                'apikey' => $rs['apikey'],
                'invoicenumerationkind' => $rs['invoicenumerationkind'],
                'uploaderenabled' => $rs['uploaderenabled'],
                'uploadmaxfilesize' => $rs['uploadmaxfilesize'],
                'uploadchunksize' => $rs['uploadchunksize'],
                'uploadextensions' => $rs['uploadextensions'],
                'sendingo' => $rs['sendingo'],
                'pageschemeid' => $rs['pageschemeid'],
                'theme' => $rs['templatefolder'],
                'pageschemeid' => $rs['pageschemeid'],
                'contactid' => $rs['contactid'],
                'wwwredirection' => $rs['wwwredirection']
            );
            
            if (! $this->fixedUrl){
                if ($rs['wwwredirection']){
                    if (strncmp(App::getHost(), 'www.', 4) === 0){
                        App::redirectSeo(App::getHttps() . '://' . substr(App::getHost(), 4) . $this->getScriptUri());
                    }
                }
                else 
                    if ($rs['wwwredirection'] === '0'){
                        if (strncmp(App::getHost(), 'www.', 4) !== 0){
                            App::redirectSeo(App::getHttps() . '://www.' . App::getHost() . $this->getScriptUri());
                        }
                    }
            }
            
            $this->container->get('session')->setActiveShopName($this->layer['shopname']);
            if (is_null($this->layer['photoid'])){
                $this->layer['photoid'] = 'logo.png';
            }
            if (is_null($this->layer['favicon'])){
                $this->layer['favicon'] = 'favicon.ico';
            }
            $this->container->get('session')->setActiveShopCurrencyId($this->layer['idcurrency']);
            $this->container->get('session')->setActiveForceLogin($this->layer['forcelogin']);
            
            if ($this->container->get('session')->getActiveBrowserData() == NULL){
                $browser = new Browser();
                $Data = Array(
                    'browser' => $browser->getBrowser(),
                    'platform' => $browser->getPlatform(),
                    'ismobile' => $browser->isMobile(),
                    'isbot' => $browser->isRobot()
                );
                $this->container->get('session')->setActiveBrowserData($Data);
            }
        }
    }

    public function getParam ($param)
    {
        return (isset($this->layer[$param])) ? $this->layer[$param] : NULL;
    }

    public function getCurrentLayer ()
    {
        return $this->layer;
    }

    public function getLayerViewId ()
    {
        return (isset($this->layer['idview'])) ? $this->layer['idview'] : 0;
    }

    public function getCurrentNamespace ()
    {
        return (isset($this->layer['namespace'])) ? $this->layer['namespace'] : 'core';
    }

    public function getSystemNamespace ()
    {
        return self::SYSTEM_NAMESPACE;
    }

    public function isOffline ()
    {
        return (boolean) $this->layer['offline'];
    }

    public function getNamespaces ()
    {
        if (isset($this->layer['namespace'])){
            return array_unique(Array(
                self::SYSTEM_NAMESPACE,
                ucfirst(strtolower($this->layer['namespace']))
            ));
        }
        return Array(
            self::SYSTEM_NAMESPACE
        );
    }
}