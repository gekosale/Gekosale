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
use Gekosale\Core\Form\Elements\Field;

class DateTo extends Rule
{
    protected $_compareWith;

    public function __construct($errorMsg, Field $compareWith)
    {
        parent::__construct($errorMsg);
        $this->_compareWith = $compareWith;
    }

    protected function _Check($value)
    {
        if (strlen($value) > 0 && strlen($this->_compareWith->GetValue()) > 0) {
            return ($value >= $this->_compareWith->GetValue());
        }
        return true;
    }

    public function Render()
    {
        $errorMsg = addslashes($this->_errorMsg);
        $field    = addslashes($this->_compareWith->getName());
        return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}', sFieldToCompare: '{$field}'}";
    }
}
