<?php
/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: registry.class.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale;

class Registry
{
	
	protected $vars = array();

	public function __set ($index, $value)
	{
		preg_match('/(?<plugin>[a-zA-Z0-9]*[\/])?(?<obj>[a-zA-Z0-9]*)(?<type>Model)?$/', $index, $matches);
		$object = $matches['obj'];
		if (isset($matches['type'])){
			switch ($matches['type']) {
				case 'Model':
					if (isset($matches['plugin'])){
						$plugin = $matches['plugin'];
					}
					$this->vars['models'][$plugin][$object] = Array(
						'value' => $value,
						'count' => 1
					);
					break;
			}
			return;
		}
		else{
			$this->vars['system'][$object] = Array(
				'value' => $value,
				'count' => 1
			);
			return;
		}
		throw new Exception('Object not saved in registry');
	}

	/**
	 *
	 * @get variables
	 *
	 * @param mixed $index
	 *
	 * @return mixed
	 *
	 */
	public function __get ($index)
	{
		preg_match('/(?<plugin>[a-zA-Z0-9]*[\/])?(?<obj>[a-zA-Z0-9]*)(?<type>Model)?$/', $index, $matches);
		$object = $matches['obj'];
		if (isset($matches['type'])){
			switch ($matches['type']) {
				case 'Model':
					if (isset($matches['plugin'])){
						$plugin = $matches['plugin'];
					}
					if (! isset($this->vars['models'][$plugin][$object]))
						break;
					$this->vars['models'][$plugin][$object]['count'] ++;
					return $this->vars['models'][$plugin][$object]['value'];
			}
		}
		else{
			if (isset($this->vars['system'][$object])){
				$this->vars['system'][$object]['count'] ++;
				return $this->vars['system'][$object]['value'];
			}
		}
		return false;
	}

}