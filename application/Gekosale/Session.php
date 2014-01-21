<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o licencji i autorach.
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
 * $Id: session.class.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale;
use Gekosale\Session\SessionInterface;

class Session
{

    protected $storage;

    public function __construct (SessionInterface $storage)
    {
        $this->storage = $storage;
        
        if (! isset($_SESSION)){
            if (isset($_POST[session_name()])){
                session_id($_POST[session_name()]);
            }
        }
        
        session_set_save_handler(Array(
            &$this,
            'open'
        ), Array(
            &$this,
            'close'
        ), Array(
            &$this,
            'read'
        ), Array(
            &$this,
            'write'
        ), Array(
            &$this,
            'destroy'
        ), Array(
            &$this,
            'gc'
        ));
        
        session_start();
    }

    public function __call ($name, $params)
    {
        if (substr($name, 0, 9) == "setActive" && strlen($name) > 9){
            $name = preg_replace('/setActive?/', '', $name);
            $_SESSION['CurrentState'][$name] = $params;
            return (true);
        }
        elseif (substr($name, 0, 9) == "getActive" && strlen($name) > 9){
            $name = preg_replace('/getActive?/', '', $name);
            if (isset($_SESSION['CurrentState'][$name])){
                if (count($_SESSION['CurrentState'][$name]) == 1)
                    return ($_SESSION['CurrentState'][$name][0]);
                else 
                    if (count($_SESSION['CurrentState'][$name]) > 1)
                        return ($_SESSION['CurrentState'][$name]);
                    else
                        return (null);
            }
        }
        elseif (substr($name, 0, 11) == "setVolatile" && strlen($name) > 11){
            $name = preg_replace('/setVolatile?/', '', $name);
            $_SESSION['CurrentState']['temp'][$name] = $params;
            return (true);
        }
        elseif (substr($name, 0, 11) == "getVolatile" && strlen($name) > 11){
            $name = preg_replace('/getVolatile?/', '', $name);
            if (isset($_SESSION['CurrentState']['temp'][$name])){
                if (count($_SESSION['CurrentState']['temp'][$name]) == 1)
                    return ($_SESSION['CurrentState']['temp'][$name][0]);
                else 
                    if (count($_SESSION['CurrentState']['temp'][$name]) > 1)
                        return ($_SESSION['CurrentState']['temp'][$name]);
                    else
                        return (null);
            }
        }
        elseif (substr($name, 0, 11) == "unsetActive" && strlen($name) > 11){
            $name = preg_replace('/unsetActive/', '', $name);
            if (isset($_SESSION['CurrentState'][$name])){
                unset($_SESSION['CurrentState'][$name]);
            }
        }
        else{
            throw new Exception('Undefined framework method: ' . $name);
        }
    }

    public static function flush ()
    {
        session_destroy();
        App::getRegistry()->core->setEnvironmentVariables();
    }

    public static function clearTemp ()
    {
        if (isset($_SESSION['CurrentState'])){
            if (isset($_SESSION['CurrentState']['temp'])){
                foreach ($_SESSION['CurrentState']['temp'] as $key => $value){
                    if (is_array($value)){
                        if (isset($value[1]) && $value[1] === true){
                            unset($_SESSION['CurrentState']['temp'][$key]);
                        }
                        else{
                            $_SESSION['CurrentState']['temp'][$key][1] = true;
                        }
                    }
                    else{
                        if ($value === true){
                            unset($_SESSION['CurrentState']['temp'][$key]);
                        }
                        else{
                            $_SESSION['CurrentState']['temp'][$key] = true;
                        }
                    }
                }
            }
        }
    }

    public function flushTemp ()
    {
        $_SESSION['CurrentState']['temp'] = Array();
    }

    public function killSession ()
    {
        session_destroy();
        App::getRegistry()->core->setEnvironmentVariables();
    }

    public function open ()
    {
        return $this->storage->open();
    }

    public function close ()
    {
        return $this->storage->close();
    }

    public function read ($sessionid)
    {
        return $this->storage->read($sessionid);
    }

    public function write ($sessionid, $sessioncontent)
    {
        return $this->storage->write($sessionid, $sessioncontent);
    }

    public function destroy ($sessionid)
    {
        return $this->storage->destroy($sessionid);
    }

    public function gc ($lifeTime)
    {
        return $this->storage->gc($lifeTime);
    }
}