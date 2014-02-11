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

class LanguageUnique extends Rule
{

    protected $_table;

    protected $_column;

    protected $_id;

    protected $_exclude;

    protected $_jsFunction;

    protected $_valueProcessFunction;

    protected $_language;

    protected static $_nextId = 0;

    public function __construct ($errorMsg, $table, $column, $valueProcessFunction = null, $exclude = null)
    {
        parent::__construct($errorMsg);
        $this->_table = $table;
        $this->_column = $column;
        $this->_exclude = $exclude;
        $this->_id = self::$_nextId ++;
        $this->_valueProcessFunction = $valueProcessFunction;
        $this->_jsFunction = 'CheckUniqueness_' . $this->_id;
        App::getRegistry()->xajaxInterface->registerFunction(array(
            $this->_jsFunction,
            $this,
            'doAjaxCheck'
        ));
        $this->_language = '0';
    }

    public function doAjaxCheck ($request)
    {
        $this->setLanguage($request['language']);
        return Array(
            'unique' => $this->_Check($request['value'])
        );
    }

    public function setLanguage ($language)
    {
        $this->_language = $language;
    }

    protected function _Check ($value)
    {
        if ($this->_valueProcessFunction) {
            $f = $this->_valueProcessFunction;
            $value = $f($value);
        }
        $sql = "
			SELECT
				COUNT(*) AS items_count
			FROM
				{$this->_table}
			WHERE
				{$this->_column} = :value
				AND languageid = :language
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
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('value', $value);
        $stmt->bindValue('language', $this->_language);
        try {
            $stmt->execute();
            $rs = $stmt->fetch();
            if ($rs['items_count'] == 0) {
                return true;
            }
        }
        catch (Exception $e) {
            throw new Exception('Error while executing sql query: ' . $e->getMessage());
        }
        return false;
    }

    public function render ()
    {
        $errorMsg = addslashes($this->_errorMsg);
        return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}', fCheckFunction: xajax_{$this->_jsFunction}}";
    }
}
