<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace Gekosale\Core;

use Symfony\Component\DependencyInjection\ContainerInterface;
use xajax;
use xajaxResponse;

/**
 * Class XajaxManager
 *
 * Provides interface for managing xajax requests, responses and function registration
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class XajaxManager
{

    /**
     * Registered Xajax callbacks
     *
     * @var array
     */
    protected $callbacks = Array();

    /**
     * Index required to make unique auto-callbacks
     *
     * @var int
     */
    protected $autoId = 0;

    /**
     * Container instance
     *
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     *
     * @param string  $name
     * @param unknown $callback
     */
    public function registerCallback($name, $callback)
    {
        $this->callbacks[$name] = $callback;
    }

    /**
     * Register multiple Xajax functions
     *
     * @param array $functions
     */
    public function registerFunctions(array $functions)
    {
        foreach ($functions as $name => $callback) {
            $this->container->get('xajax')->registerFunction([
                $name,
                $callback[0],
                $callback[1]
            ]);
        }
    }

    /**
     * Register Xajax function
     *
     * @param array $registrationArray
     *
     * @return string
     */
    public function registerFunction(array $registrationArray)
    {
        $name         = array_shift($registrationArray);
        $callback     = $registrationArray;
        $callbackName = '_auto_callback_' . $this->autoId++;
        $this->registerCallback($callbackName, $callback);
        $this->container->get('xajax')->registerFunction(Array(
            $name,
            $this,
            $callbackName
        ));
        return 'xajax_' . $name;
    }

    /**
     * Call dynamically registered Xajax function
     *
     * @param $name
     * @param $arguments
     *
     * @return xajaxResponse
     */
    public function __call($name, $arguments)
    {
        $request         = $arguments[0];
        $responseHandler = $arguments[1];
        $objResponse     = new xajaxResponse();
        $response        = call_user_func($this->callbacks[$name], $request);
        if (!is_array($response)) {
            $response = Array();
        }
        $objResponse->script("{$responseHandler}(" . json_encode($response) . ")");

        return $objResponse;
    }
}