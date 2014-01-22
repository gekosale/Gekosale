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
namespace Gekosale\Plugin;

class TagTranslatorModel extends Component\Model
{

	public function __construct ($registry)
	{
		parent::__construct($registry);
	}

	public function Translate ($format, $tags, $values)
	{
		$lookupTable = Array();
		foreach ($tags as $tag){
			if (isset($values[$tag]) && ! empty($values[$tag])){
				$lookupTable['{' . $tag . '}'] = $values[$tag];
			}
			else{
				$lookupTable['{' . $tag . '}'] = '';
			}
		}
		return strtr($format, $lookupTable);
	}
}