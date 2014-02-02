<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @category    Gekosale 
 * @package     Gekosale\Core\Form 
 * @subpackage  Gekosale\Core\Form\Rule
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Form\Rule;

use Gekosale\Core\Form\Rule;

class Custom extends Rule
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
        foreach ($this->_params as $paramName => $paramValue) {
            if ($paramValue instanceof Node) {
                $params[$paramName] = $paramValue->GetValue();
            }
            else {
                $params[$paramName] = $paramValue;
            }
        }
        return call_user_func($this->_checkFunction, $value, $params);
    }

    public function Render ()
    {
        $errorMsg = addslashes($this->_errorMsg);
        $params = Array();
        foreach ($this->_params as $paramName => $paramValue) {
            if ($paramValue instanceof \FormEngine\Node) {
                $params['_field_' . $paramName] = $paramValue->GetName();
            }
            else {
                $params[$paramName] = $paramValue;
            }
        }
        return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}', fCheckFunction: {$this->_jsFunction}, oParams: " . json_encode($params) . "}";
    }
}
