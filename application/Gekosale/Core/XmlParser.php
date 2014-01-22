<?php
/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o
 * licencji i autorach.
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
 * $Id: xmlparser.class.php 438 2011-08-27 09:29:36Z gekosale $
 */

namespace Gekosale\Core;

use Exception;

class XMLParser
{
	
	protected $fileName = NULL;
	protected $parsered = NULL;

	public function load ($fileName)
	{
		if (! is_file($fileName)){
			throw new Exception('File doesn\'t exists: ' . $fileName);
		}
		$this->fileName = $fileName;
	}

	public function parse ()
	{
		if ($this->fileName == NULL){
			throw new CoreException('File not loaded');
		}
		$this->parsered = @simplexml_load_file($this->fileName);
		if ($this->parsered === false){
			throw new Exception('Opening and ending tag mismatch in: ' . $this->fileName);
		}
	}

	public function getParsered ()
	{
		return $this->parsered;
	}

	public function parseExternal ($fileUrl)
	{
		$this->parsered = @simplexml_load_file($fileUrl);
		return $this->parsered;
	}

	public function parseFast ($fileName)
	{
		try{
			$this->load($fileName);
			$this->parse();
		}
		catch (Exception $e){
			throw $e;
		}
		return $this->getParsered();
	}

	public function getValue ($nodeArray, $arrayMode = true)
	{
		$subnodes = explode('/', $nodeArray);
		$curnode = $this->getParsered()->children();
		$curnode = $this->loop($this->getParsered()->children(), $subnodes);
		if ($arrayMode == true){
			return (array) $curnode;
		}
		return $curnode;
	}

	protected function loop ($curnode, $nodes)
	{
		foreach ($curnode as $child){
			if ($child->getName() == current($nodes)){
				if (! next($nodes)){
					return $child;
				}
				$this->loop($child, $nodes);
			}
		}
	}

	public function getValueToString ($node)
	{
		return (string) $this->getValue($node, 0);
	}

	public function flush ()
	{
		$this->fileName = NULL;
		$this->parsered = NULL;
	}
}