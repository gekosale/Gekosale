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

class Form extends Component
{

    public function AddForm ($options)
    {
        return new Form\Element\Form($options, $this->container);
    }

    public function AddRuleRequired ($errorMsg)
    {
        return new Form\Rule\Required($errorMsg);
    }

    public function AddRuleUnique ($errorMsg, $table, $column, $valueProcessFunction = null, $exclude = null)
    {
        return new Form\Rule\Unique($this->container, $errorMsg, $table, $column, $valueProcessFunction, $exclude);
    }

    public function AddFilterCommaToDotChanger ()
    {
        return new Form\Filter\CommaToDotChanger();
    }

    public function AddFilterNoCode ()
    {
        return new Form\Filter\NoCode();
    }

    public function AddFilterTrim ()
    {
        return new Form\Filter\Trim();
    }

    public function AddFilterSecure ()
    {
        return new Form\Filter\Secure();
    }
}