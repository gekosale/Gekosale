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
 * $Id: formprotection.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale\Plugin;

use HTMLPurifier;

require_once ROOTPATH . 'lib' . DS . 'HTMLPurifier' . DS . 'HTMLPurifier.standalone.php';
class FormprotectionModel extends Component\Model
{

	public function filterArray (Array $Data)
	{
		foreach ($Data as $k => $v){
			$Data[$k] = $this->cropDangerousCode($v);
		}
		return $Data;
	}

	public function cropDangerousCode ($value)
	{
		if (strlen($value) > 0){
			$purifier = new HTMLPurifier();
			$clean = $purifier->purify($value);
			return $clean;
		}
		else{
			return $value;
		}
	}

	function cropDangerousCodeSubmitedValues ($tabStrings)
	{
		$Data = Array();
		
		foreach ($tabStrings as $string => $key){
			
			// JAVASCRIPT, XSS, AHREF, ON*
			$key = preg_replace("/href=(['\"]).*?javascript:(.*)?\\1/i", "", $key);
			$key = preg_replace("/<(.*)?javascript.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", "", $key);
			$key = preg_replace("/:expression\(.*?((?>[^ (.*?)]+)|(?R)).*?\)\)/i", "", $key);
			$key = preg_replace("/<(.*)?:expr.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", "", $key);
			
			// wyczyszczenie wszystkich zdarzeń zaczynających się od on*
			// (onclick, onfocus, onmouseout, etc.)
			while (preg_match("/<(.*)?\s?on.+?=?\s?.+?(['\"]).*?\\2\s?(.*)?>/i", $key))
				$key = preg_replace("/<(.*)?\s?on.+?=?\s?.+?(['\"]).*?\\2\s?(.*)?>/i", "", $key);
			
			$key = preg_replace("/<(.*)?(\[cdata\[(.*?)\]\])?(.*)?(\]{2})?>/i", "", $key);
			
			// HTML
			$key = strip_tags($key);
			
			// PHP
			$key = preg_replace("/(.*)?(\<\?php)+(.*)?(\?\>)+(.*)?>/i", "", $key);
			
			$Data[$string] = $key;
		}
		
		if ($Data == $tabStrings)
			return true;
		else
			return false;
	}
}