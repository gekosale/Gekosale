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

class DateTo extends Rule
{

    protected $_compareWith;

    public function __construct ($errorMsg, $compareWith)
    {
        parent::__construct($errorMsg);
        $this->_compareWith = $compareWith;
    }

    protected function _Check ($value)
    {
        if (strlen($value) > 0 && strlen($this->_compareWith->GetValue()) > 0) {
            return ($value >= $this->_compareWith->GetValue());
        }
        return true;
    }

    public function render ()
    {
        $errorMsg = addslashes($this->_errorMsg);
        $field = addslashes($this->_compareWith->getName());
        return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}', sFieldToCompare: '{$field}'}";
    }
}
