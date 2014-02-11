<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @category    Gekosale 
 * @package     Gekosale\Core\Form 
 * @subpackage  Gekosale\Core\Form\Rule
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Form\Rule;

use Gekosale\Core\Form\Rule;

class Format extends Rule
{

    protected $_format;

    public function __construct ($errorMsg, $format)
    {
        parent::__construct($errorMsg);
        $this->_format = $format;
    }

    protected function check ($value)
    {
        if (strlen($value) == 0) {
            return true;
        }
        return (preg_match($this->_format, $value) == 1);
    }

    public function render ()
    {
        $format = addslashes($this->_format);
        $errorMsg = addslashes($this->_errorMsg);
        return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}', sFormat: '{$format}'}";
    }
}
