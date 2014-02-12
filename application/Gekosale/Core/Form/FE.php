<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @category    Gekosale 
 * @package     Gekosale\Core\Form 
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Form;

class FE
{

    const INFINITE = 'inf';

    const TYPE_NUMBER = 'number';

    const TYPE_STRING = 'string';

    const TYPE_FUNCTION = 'function';

    const TYPE_ARRAY = 'array';

    const TYPE_OBJECT = 'object';

    const TYPE_BOOLEAN = 'boolean';

    public static function SubmittedData ()
    {
        return $_POST;
    }

    public static function isAction ($actionName)
    {
        $actionName = '_Action_' . $actionName;
        return (isset($_POST[$actionName]) && ($_POST[$actionName] == '1'));
    }
}
