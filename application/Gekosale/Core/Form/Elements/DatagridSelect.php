<?php
/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms 
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the 
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */

namespace FormEngine\Elements;
use Gekosale\App as App;
use FormEngine\FE as FE;

class DatagridSelect extends Select
{
	
	public $datagrid;
	
	protected $_jsFunction;
	
	const SORT_DIR_ASC = 1;
	const SORT_DIR_DESC = 2;
	
	const ALIGN_LEFT = 1;
	const ALIGN_CENTER = 2;
	const ALIGN_RIGHT = 3;
	
	const FILTER_NONE = 0;
	const FILTER_INPUT = 1;
	const FILTER_BETWEEN = 2;
	const FILTER_SELECT = 3;
	const FILTER_AUTOSUGGEST = 4;
	
	const WIDTH_AUTO = 0;

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
		if (! isset($this->_attributes['key'])){
			throw new Exception("Datagrid key (attribute: key) not set for field '{$this->_attributes['name']}'.");
		}
		if (! isset($this->_attributes['columns'])){
			throw new Exception("Datagrid columns (attribute: columns) not set for field '{$this->_attributes['name']}'.");
		}
		if (! isset($this->_attributes['datagrid_init_function']) || ! is_callable($this->_attributes['datagrid_init_function'])){
			throw new Exception("Datagrid initialization function not set (attribute: datagrid_init_function) for field '{$this->_attributes['name']}'. Hint: check whether the method you have specified is public.");
		}
		$this->_jsFunction = 'LoadRecords_' . $this->_id;
		$this->_attributes['jsfunction'] = 'xajax_' . $this->_jsFunction;
		App::getRegistry()->xajax->registerFunction(array(
			$this->_jsFunction,
			$this,
			'loadRecords_' . $this->_id
		));
	}

	protected function _PrepareAttributes_JS ()
	{
		$attributes = Array(
			$this->_FormatAttribute_JS('name', 'sName'),
			$this->_FormatAttribute_JS('label', 'sLabel'),
			$this->_FormatAttribute_JS('comment', 'sComment'),
			$this->_FormatAttribute_JS('error', 'sError'),
			$this->_FormatAttribute_JS('jsfunction', 'fLoadRecords', FE::TYPE_FUNCTION),
			$this->_FormatAttribute_JS('key', 'sKey'),
			$this->_FormatAttribute_JS('columns', 'aoColumns', FE::TYPE_OBJECT),
			$this->_FormatAttribute_JS('selected_columns', 'aoSelectedColumns', FE::TYPE_OBJECT),
			$this->_FormatRepeatable_JS(),
			$this->_FormatRules_JS(),
			$this->_FormatDependency_JS(),
			$this->_FormatDefaults_JS()
		);
		return $attributes;
	}

	public function loadRecords ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function getDatagrid ()
	{
		if ($this->datagrid == NULL){
			$this->datagrid = App::getModel('datagrid/datagrid');
			call_user_func($this->_attributes['datagrid_init_function'], $this->datagrid);
		}
		return $this->datagrid;
	}

	public function __call ($name, $args)
	{
		if (substr($name, 0, 11) == 'loadRecords'){
			return call_user_func(Array(
				$this,
				'loadRecords'
			), $args[0], $args[1]);
		}
	}

}
