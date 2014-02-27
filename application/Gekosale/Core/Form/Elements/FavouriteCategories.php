<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace FormEngine\Elements;

use Gekosale\App as App;
use Gekosale\Translation as Translation;
use FormEngine\FE as FE;

class FavouriteCategories extends Tree
{
	protected $_jsGetSelectedInfo;

	public function __construct ($attributes)
	{
		parent::__construct($attributes);
		$this->_jsGetSelectedInfo = 'GetSelectedInfo_' . $this->_id;
		if (isset($this->_attributes['load_selected_info']) && is_callable($this->_attributes['load_selected_info'])){
			$this->_attributes['get_selected_info'] = 'xajax_' . $this->_jsGetSelectedInfo;
			App::getRegistry()->xajaxInterface->registerFunction(array(
				$this->_jsGetSelectedInfo,
				$this,
				'getSelectedInfo'
			));
		}
	}

	public function getSelectedInfo ($request)
	{
		$rows = Array();
		if (! is_array($request['id'])){
			$request['id'] = Array(
				$request['id']
			);
		}
		foreach ($request['id'] as $rowId){
			$path = call_user_func($this->_attributes['load_selected_info'], $rowId);
			$pathSize = count($path);
			if ($pathSize === 0) {
				$path = array();
			}
			else {
				$path[$pathSize - 1] = '<strong>' . $path[$pathSize - 1] . '</strong>';
				if ($pathSize > 5){
					$path = array_slice($path, $pathSize - 5);
					array_unshift($path, '...');
				}
			}

			$rows[] = Array(
				'id' => $rowId,
				'values' => Array(
					implode(' / ', $path)
				)
			);
		}
		return Array(
			'rows' => $rows
		);
	}

	protected function _PrepareAttributes_JS ()
	{
		$attributes = parent::_PrepareAttributes_JS();
		$attributes[] = $this->_FormatAttribute_JS('get_selected_info', 'fGetSelectedInfo', FE::TYPE_FUNCTION);
		$attributes[] = $this->_FormatAttribute_JS('columns', 'aoColumns', FE::TYPE_OBJECT);
		return $attributes;
	}
}
