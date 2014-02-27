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

class VAT extends Format
{

    public function __construct($errorMsg)
    {
        parent::__construct($errorMsg, '/^\.*/');
    }

    public function Render()
    {
        $errorMsg = addslashes($this->_errorMsg);
        return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}'}";
    }

}
