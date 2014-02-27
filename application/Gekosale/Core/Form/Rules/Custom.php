<?php
/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the
 * Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it
 * through the
 * world-wide-web, please send an email to license@verison.pl so we can send you
 * a copy immediately.
 */

namespace FormEngine\Rules;
use Gekosale\App as App;

class Custom extends \FormEngine\Rule
{
	
	protected $_checkFunction;
	protected $_jsFunction;
	protected $_params;
	
	protected static $_nextId = 0;

	public function __construct ($errorMsg, $checkFunctionCallback, $params = Array())
	{
		parent::__construct($errorMsg);
		$this->_checkFunction = $checkFunctionCallback;
		$this->_jsFunction = App::getRegistry()->xajaxInterface->registerFunction(array(
			'CheckCustomRule_' . self::$_nextId ++,
			$this,
			'doAjaxCheck'
		));
		$this->_params = $params;
	}

	public function doAjaxCheck ($request)
	{
		return Array(
			'unique' => call_user_func($this->_checkFunction, $request['value'], $request['params'])
		);
	}

	protected function _Check ($value)
	{
		$params = Array();
		foreach ($this->_params as $paramName => $paramValue){
			if ($paramValue instanceof Node){
				$params[$paramName] = $paramValue->GetValue();
			}
			else{
				$params[$paramName] = $paramValue;
			}
		}
		return call_user_func($this->_checkFunction, $value, $params);
	}

	public function Render ()
	{
		$errorMsg = addslashes($this->_errorMsg);
		$params = Array();
		foreach ($this->_params as $paramName => $paramValue){
			if ($paramValue instanceof \FormEngine\Node){
				$params['_field_' . $paramName] = $paramValue->GetName();
			}
			else{
				$params[$paramName] = $paramValue;
			}
		}
		return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}', fCheckFunction: {$this->_jsFunction}, oParams: " . json_encode($params) . "}";
	}

}
