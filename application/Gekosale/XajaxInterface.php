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
 * $Id: xajaxinterface.class.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale;
use xajaxResponse;

class XajaxInterface
{

    protected $callbacks;

    protected $autoId;

    public function __construct ()
    {
        $this->callbacks = Array();
        $this->autoId = 0;
    }

    public function registerCallback ($name, $callback)
    {
        $this->callbacks[$name] = $callback;
    }

    public function registerFunction ($registrationArray)
    {
        $name = array_shift($registrationArray);
        $callback = $registrationArray;
        $callbackName = '_auto_callback_' . $this->autoId ++;
        $this->registerCallback($callbackName, $callback);
        App::getRegistry()->xajax->registerFunction(Array(
            $name,
            $this,
            $callbackName
        ));
        return 'xajax_' . $name;
    }

    public function __call ($name, $arguments)
    {
        try{
            $request = $arguments[0];
            $responseHandler = $arguments[1];
            $objResponse = new xajaxResponse();
            $response = call_user_func($this->callbacks[$name], $request);
            if (! is_array($response)){
                $response = Array();
            }
            $objResponse->script("{$responseHandler}(" . json_encode($response) . ")");
        }
        catch (Exception $e){
            $objResponse = new xajaxResponse();
            $objResponse->script("GError('" . _('ERR_PROBLEM_DURING_AJAX_EXECUTION') . "', '" . preg_replace('/(\n|\r)+/', '\n', nl2br(addslashes($e->getMessage()))) . "');");
        }
        return $objResponse;
    }
}