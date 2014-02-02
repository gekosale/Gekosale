<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Core
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core;

use Symfony\Component\DependencyInjection\ContainerInterface;
use xajaxResponse;

class XajaxInterface
{

    protected $callbacks;

    protected $autoId;

    protected $container;

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
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
        $this->container->get('xajax')->registerFunction(Array(
            $name,
            $this,
            $callbackName
        ));
        return 'xajax_' . $name;
    }

    public function __call ($name, $arguments)
    {
        try {
            $request = $arguments[0];
            $responseHandler = $arguments[1];
            $objResponse = new xajaxResponse();
            $response = call_user_func($this->callbacks[$name], $request);
            if (! is_array($response)) {
                $response = Array();
            }
            $objResponse->script("{$responseHandler}(" . json_encode($response) . ")");
        }
        catch (Exception $e) {
            $objResponse = new xajaxResponse();
            $objResponse->script("GError('" . _('ERR_PROBLEM_DURING_AJAX_EXECUTION') . "', '" . preg_replace('/(\n|\r)+/', '\n', nl2br(addslashes($e->getMessage()))) . "');");
        }
        return $objResponse;
    }
}