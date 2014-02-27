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

use Gekosale\Core\Form\Rule;

class Format extends Rule
{

    protected $_format;

    public function __construct($errorMsg, $format)
    {
        parent::__construct($errorMsg);
        $this->_format = $format;
    }

    protected function _Check($value)
    {
        if (strlen($value) == 0) {
            return true;
        }
        return (preg_match($this->_format, $value) == 1);
    }

    public function Render()
    {
        $format   = addslashes($this->_format);
        $errorMsg = addslashes($this->_errorMsg);
        return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}', sFormat: '{$format}'}";
    }

}
