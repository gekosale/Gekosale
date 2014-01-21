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
namespace Gekosale\Cache\Storage;
use Exception;

class File
{

    protected $path;

    protected $cacheid;

    protected $suffix = '.reg';

    public function __construct ()
    {
        $this->path = ROOTPATH . 'serialization' . DS;
        $this->cacheid = \Gekosale\Helper::getViewId() . '_' . \Gekosale\Helper::getLanguageId();
    }

    public function save ($name, $value, $time)
    {
        if (@file_put_contents($this->getCacheFileName($name), $value, LOCK_EX) === FALSE){
            throw new Exception('Can not serialize content to file ' . $this->getCacheFileName($name) . '. Check directory\'s permissions');
        }
        
        $time = time() + ($time ?  : 2592000);
        touch($this->getCacheFileName($name), $time, $time);
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
        foreach (glob($this->path . '*' . $this->suffix) as $fn){
            @unlink($fn);
        }
    }

    public function getCacheFileName ($name)
    {
        $cacheid = \Gekosale\Helper::getViewId() . '_' . \Gekosale\Helper::getLanguageId();
        return $this->path . strtolower($name) . '_' . $cacheid . $this->suffix;
    }
}
