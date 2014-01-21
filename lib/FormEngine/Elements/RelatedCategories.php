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

class RelatedCategories extends FavouriteCategories
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
		if (! is_array($request['shop_id'])){
			$request['shop_id'] = Array(
				$request['shop_id']
			);
		}
		foreach ($request['id'] as $i => $rowId){
			$paths = call_user_func($this->_attributes['load_selected_info'], $rowId, $request['shop_id'][$i]);
			$allegroPath = $paths['allegro'];
			$shopPath = (array)$paths['shop'];
			$allegroPathSize = count($allegroPath);
			$shopPathSize = count($shopPath);
			$allegroPath[$allegroPathSize - 1] = '<strong>' . $allegroPath[$allegroPathSize - 1] . '</strong>';
			$shopPath[$shopPathSize - 1] = '<strong>' . $shopPath[$shopPathSize - 1] . '</strong>';
			if ($allegroPathSize > 3){
				$allegroPath = array_slice($allegroPath, $allegroPathSize - 3);
				array_unshift($allegroPath, '...');
			}
			if ($shopPathSize > 3){
				$shopPath = array_slice($shopPath, $shopPathSize - 3);
				array_unshift($shopPath, '...');
			}
			$rows[] = Array(
				'id' => $rowId,
				'values' => Array(
					implode(' / ', $allegroPath),
					implode(' / ', $shopPath)
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
		$attributes[] = $this->_FormatAttribute_JS('shop_categories', 'aoShopCategories', FE::TYPE_OBJECT);
		return $attributes;
	}
}
