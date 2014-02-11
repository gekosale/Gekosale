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

class Unique extends Rule
{

    protected $_container;

    protected $_table;

    protected $_column;

    protected $_id;

    protected $_exclude;

    protected $_jsFunction;

    protected $_valueProcessFunction;

    protected static $_nextId = 0;

    public function __construct ($container, $errorMsg, $table, $column, $valueProcessFunction = null, $exclude = null)
    {
        parent::__construct($errorMsg);
        $this->_container = $container;
        $this->_table = $table;
        $this->_column = $column;
        $this->_exclude = $exclude;
        $this->_id = self::$_nextId ++;
        $this->_valueProcessFunction = $valueProcessFunction;
        $this->_jsFunction = 'CheckUniqueness_' . $this->_id;
        $this->_container->get('xajax.interface')->registerFunction(array(
            $this->_jsFunction,
            $this,
            'doAjaxCheck'
        ));
    }

    public function doAjaxCheck ($request)
    {
        return Array(
            'unique' => $this->_Check($request['value'])
        );
    }

    protected function _Check ($value)
    {
        if ($this->_valueProcessFunction) {
            $value = call_user_func($this->_valueProcessFunction, $value);
        }
        
        $sql = "
			SELECT
				COUNT(*) AS items_count
			FROM
				{$this->_table}
			WHERE
				{$this->_column} = :value
		";
        if ($this->_exclude && is_array($this->_exclude)) {
            if (! is_array($this->_exclude['values'])) {
                $this->_exclude['values'] = Array(
                    $this->_exclude['values']
                );
            }
            $excludedValues = implode(', ', $this->_exclude['values']);
            $sql .= "AND NOT {$this->_exclude['column']} IN ({$excludedValues})";
        }
        $stmt = $this->_container->get('propel.connection')->prepare($sql);
        $stmt->bindValue('value', $value);
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs['items_count'] == 0) {
            return true;
        }
        return false;
    }

    public function render ()
    {
        $errorMsg = addslashes($this->_errorMsg);
        return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}', fCheckFunction: xajax_{$this->_jsFunction}}";
    }
}
