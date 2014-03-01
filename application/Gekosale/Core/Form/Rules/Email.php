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

namespace Gekosale\Core\Form\Rules;

use Gekosale\Core\Form\Rules\Format;

/**
 * Class Email
 *
 * Checks if field value is valid e-mail
 *
 * @package Gekosale\Core\Form\Rules
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Email extends Format implements RuleInterface
{

    public function __construct($errorMsg)
    {
        parent::__construct($errorMsg, '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.(?:[A-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum|pro)$/i');
    }

    public function checkValue($value)
    {
        if (strlen($value) == 0) {
            return true;
        }

        return (preg_match($this->_format, $value) == 1);
    }

    public function render()
    {
        $errorMsg = addslashes($this->_errorMsg);

        return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}'}";
    }

}
