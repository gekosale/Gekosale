<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the
 * Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it
 * through the
 * world-wide-web, please send an email to license@verison.pl so we can send you
 * a copy immediately.
 */
namespace FormEngine\Rules;

use Gekosale\App as App;
use Gekosale\Db as Db;

class Unique extends \FormEngine\Rule
{
	protected $_table;
	protected $_column;
	protected $_id;
	protected $_exclude;
	protected $_jsFunction;
	protected $_valueProcessFunction;
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
	}

	public function doAjaxCheck ($request)
	{
		return Array(
			'unique' => $this->_Check($request['value'])
		);
	}

	protected function _Check ($value)
	{
		if ($this->_valueProcessFunction){
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
		if ($this->_exclude and is_array($this->_exclude)){
			if (! is_array($this->_exclude['values'])){
				$this->_exclude['values'] = Array(
					$this->_exclude['values']
				);
			}
			$excludedValues = implode(', ', $this->_exclude['values']);
			$sql .= "AND NOT {$this->_exclude['column']} IN ({$excludedValues})";
		}
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('value', $value);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs['items_count'] == 0){
				return true;
			}
		}
		catch (Exception $e){
			throw new Exception('Error while executing sql query: ' . $e->getMessage());
		}
		return false;
	}

	public function Render ()
	{
		$errorMsg = addslashes($this->_errorMsg);
		return "{sType: '{$this->GetType()}', sErrorMessage: '{$errorMsg}', fCheckFunction: xajax_{$this->_jsFunction}}";
	}
}
