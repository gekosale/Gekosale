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
namespace Gekosale\Core\Form;

use Gekosale\Core\Form\Conditions\ConditionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use xajaxResponse;

/**
 * Class Dependency
 *
 * @package Gekosale\Core\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Dependency
{

    const HIDE                   = 'HIDE';
    const SHOW                   = 'SHOW';
    const IGNORE                 = 'IGNORE';
    const SUGGEST                = 'SUGGEST';
    const INVOKE_CUSTOM_FUNCTION = 'INVOKE_CUSTOM_FUNCTION';
    const EXCHANGE_OPTIONS       = 'EXCHANGE_OPTIONS';

    public $type;
    public $registry;
    protected $id;
    protected $condition;
    protected $srcFunction;
    protected $_field;
    protected $_argument;

    protected static $_nextId = 0;

    public function __construct($type, $field, $condition, $argument = null, ContainerInterface $container)
    {
        $this->container = $container;
        $this->argument  = $argument;
        $this->type      = $type;
        if (is_object($condition) && $condition instanceof Condition) {
            $this->condition = $condition;
        } else {
            $this->srcFunction = $condition;
            $this->id          = self::$_nextId++;
            switch ($this->type) {
                case self::EXCHANGE_OPTIONS:
                    $this->jsFunction = 'GetOptions_' . $this->id;
                    $this->container->get('xajax_manager')->registerFunction([
                        $this->jsFunction,
                        $this,
                        'doAjaxOptionsRequest_' . $this->id
                    ]);
                    break;
                case self::INVOKE_CUSTOM_FUNCTION:
                    $this->jsFunction = $condition;
                    break;
                case self::SUGGEST:
                    $this->jsFunction = 'GetSuggestions_' . $this->id;
                    $this->container->get('xajax_manager')->registerFunction([
                        $this->jsFunction,
                        $this,
                        'doAjaxSuggestionRequest_' . $this->id
                    ]);
            }
        }
        $this->_field = $field;
    }

    public function evaluate($value = '', $i = null)
    {
        if (!$this->condition instanceof Condition) {
            return false;
        }

        if ($i === null) {
            return $this->condition->evaluate($this->_field->getValue());
        }
        $matchingValues = $this->_field->getValue();
        if (is_array($matchingValues)) {
            if (isset($matchingValues[$i])) {
                return $this->condition->evaluate($matchingValues[$i]);
            } else {
                return $this->condition->evaluate('');
            }
        }

        return $this->condition->evaluate($matchingValues);
    }

    public function doAjaxSuggestionRequest($request, $responseHandler)
    {
        try {
            $objResponse = new xajaxResponse();
            $response    = Array(
                'suggestion' => call_user_func($this->srcFunction, $request['value'])
            );
            $objResponse->script("{$responseHandler}(" . json_encode($response) . ")");

            return $objResponse;
        } catch (Exception $e) {
            $objResponse = new xajaxResponse();
            $objResponse->script("GAlert('{Translation::get('ERR_PROBLEM_DURING_AJAX_EXECUTION')}', '{$e->getMessage()}');");

            return $objResponse;
        }
    }

    public function doAjaxOptionsRequest($request, $responseHandler)
    {
        try {
            $objResponse = new xajaxResponse();
            if ($this->argument !== null) {
                $rawOptions = call_user_func($this->srcFunction, $request['value'], $this->argument);
            } else {
                $rawOptions = call_user_func($this->srcFunction, $request['value']);
            }
            $options = Array();
            foreach ($rawOptions as $option) {
                $value     = addslashes($option->value);
                $label     = addslashes($option->label);
                $options[] = Array(
                    'sValue' => $value,
                    'sLabel' => $label
                );
            }
            $response = Array(
                'options' => $options
            );
            $objResponse->script("{$responseHandler}(" . json_encode($response) . ")");

            return $objResponse;
        } catch (Exception $e) {
            $objResponse = new xajaxResponse();
            $objResponse->script("GAlert('{Translation::get('ERR_PROBLEM_DURING_AJAX_EXECUTION')}', '{$e->getMessage()}');");

            return $objResponse;
        }
    }

    public function renderJs()
    {
        if ($this->condition instanceof Condition) {
            return "new GFormDependency(GFormDependency.{$this->type}, '{$this->_field->form->getName()}.{$this->_field->getName()}', {$this->condition->renderJs()})";
        } else {
            switch ($this->type) {
                case self::INVOKE_CUSTOM_FUNCTION:
                    if ($this->argument !== null) {
                        $argument = json_encode($this->argument);

                        return "new GFormDependency(GFormDependency.{$this->type}, '{$this->_field->form->getName()}.{$this->_field->getName()}', {$this->jsFunction}, {$argument})";
                    }

                    return "new GFormDependency(GFormDependency.{$this->type}, '{$this->_field->form->getName()}.{$this->_field->getName()}', {$this->jsFunction})";
                    break;
                case self::EXCHANGE_OPTIONS:
                case self::SUGGEST:
                    return "new GFormDependency(GFormDependency.{$this->type}, '{$this->_field->form->getName()}.{$this->_field->getName()}', xajax_{$this->jsFunction})";
            }
        }
    }

    public function __call($name, $args)
    {
        if (substr($name, 0, 20) == 'doAjaxOptionsRequest') {
            return call_user_func(Array(
                $this,
                'doAjaxOptionsRequest'
            ), $args[0], $args[1]);
        }
        if (substr($name, 0, 23) == 'doAjaxSuggestionRequest') {
            return call_user_func(Array(
                $this,
                'doAjaxSuggestionRequest'
            ), $args[0], $args[1]);
        }
    }
}
