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

use Gekosale\Core\Form\Filter\NoCode;
use Gekosale\Core\Form\Filter\Trim;
use Gekosale\Core\Form\Filter\Secure;

/**
 * @Annotation
 */
class Form extends Component
{

    public $form;

    public $alias = NULL;

    /**
     * Shortcut for adding new Form\Element\Form
     * 
     * @param   array   $options
     * 
     * @return  object  Gekosale\Core\Form\Element\Form
     */
    public function AddForm ($options)
    {
        return new Form\Element\Form($options, $this->container);
    }

    /**
     * Shortcut for adding new Form\Rule\Required
     *
     * @param   string  $errorMsg
     *
     * @return  object  Gekosale\Core\Form\Rule\Required
     */
    public function AddRuleRequired ($errorMsg)
    {
        return new Form\Rule\Required($errorMsg);
    }

    /**
     * Shortcut for adding new Form\Rule\Unique
     *
     * @param   string  $errorMsg
     * @param   string  $table
     * @param   string  $column
     * @param   string  $valueProcessFunction
     * @param   string  $exclude
     *
     * @return  object  Gekosale\Core\Form\Rule\Unique
     */
    public function AddRuleUnique ($errorMsg, $table, $column, $valueProcessFunction = null, $exclude = null)
    {
        return new Form\Rule\Unique($this->container, $errorMsg, $table, $column, $valueProcessFunction, $exclude);
    }

    /**
     * Shortcut for adding new Form\Filter\CommaToDotChanger
     *
     * @return  object  Gekosale\Core\Form\Filter\CommaToDotChanger
     */
    public function AddFilterCommaToDotChanger ()
    {
        return new Form\Filter\CommaToDotChanger();
    }

    /**
     * Shortcut for adding new Form\Filter\NoCode
     *
     * @return  object  Gekosale\Core\Form\Filter\NoCode
     */
    public function AddFilterNoCode ()
    {
        return new Form\Filter\NoCode();
    }

    /**
     * Shortcut for adding new Form\Filter\Trim
     *
     * @return  object  Gekosale\Core\Form\Filter\Trim
     */
    public function AddFilterTrim ()
    {
        return new Form\Filter\Trim();
    }

    /**
     * Shortcut for adding new Form\Filter\Secure
     *
     * @return  object  Gekosale\Core\Form\Filter\Secure
     */
    public function AddFilterSecure ()
    {
        return new Form\Filter\Secure();
    }
}