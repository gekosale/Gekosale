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

use Gekosale\Core\Rules\RuleInterface;
use Gekosale\Core\Form\Rules\Format;

/**
 * Class VAT
 *
 * @package Gekosale\Core\Form\Rules
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class VAT extends Format implements RuleInterface
{

    public function __construct($errorMsg)
    {
        parent::__construct($errorMsg, '/^\.*/');
    }

    public function render()
    {
        $errorMsg = addslashes($this->_errorMsg);

        return "{sType: '{$this->getType()}', sErrorMessage: '{$errorMsg}'}";
    }

}
