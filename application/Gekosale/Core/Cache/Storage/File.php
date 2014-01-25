<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 *
 * $Revision: 54 $
 * $Author: krzotr $
 * $Date: 2011-04-09 09:52:26 +0200 (So, 09 kwi 2011) $
 * $Id: cache.class.php 54 2011-04-09 07:52:26Z krzotr $
 */
namespace Gekosale\Core\Cache\Storage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class File
{

    protected $path;

    protected $cacheid;

    protected $extension;

    protected $container;

    public function __construct (ContainerInterface $container, $path, $extension)
    {
        $this->container = $container;
        $this->path = $path;
        $this->extension = $extension;
        $this->cacheid = $this->container->get('helper')->getViewId() . '_' . $this->container->get('helper')->getLanguageId();
    }

    public function save ($name, $value)
    {
        $this->container->get('filesystem')->dumpFile($this->getCacheFileName($name), $value);
    }

    public function load ($name)
    {
        if (($content = @file_get_contents($this->getCacheFileName($name))) === FALSE){
            return FALSE;
        }
        
        clearstatcache();
        if (filemtime($this->getCacheFileName($name)) < time()){
            return FALSE;
        }
        
        return $content;
    }

    public function delete ($name)
    {
        foreach (glob($this->path . strtolower($name) . '*') as $key => $fn){
            @unlink($fn);
        }
    }

    public function deleteAll ()
    {
        foreach (glob($this->path . '*' . $this->extension) as $fn){
            @unlink($fn);
        }
    }

    public function getCacheFileName ($name)
    {
        $cacheid = $this->container->get('helper')->getViewId() . '_' . $this->container->get('helper')->getLanguageId();
        return $this->path . strtolower($name) . '_' . $cacheid . $this->extension;
    }
}
