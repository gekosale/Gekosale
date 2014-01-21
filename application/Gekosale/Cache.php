<?php
/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 *
 * $Revision: 54 $
 * $Author: krzotr $
 * $Date: 2011-04-09 09:52:26 +0200 (So, 09 kwi 2011) $
 * $Id: cache.class.php 54 2011-04-09 07:52:26Z krzotr $
 */

namespace Gekosale;

class Cache
{

	public $storage;

	public function __construct ($storage)
	{
		$this->storage = $storage;
	}

	public function save ($name, $value, $time = 0)
	{
		$this->storage->save($name, $this->serialize($value), $time);
	}

	public function load ($name)
	{
		return $this->unserialize($this->storage->load($name));
	}

	public function delete ($name)
	{
		$this->storage->delete($name);
	}

	public function deleteAll ()
	{
		$this->storage->deleteAll();
	}

	public function serialize ($content)
	{
		return serialize($content);
	}

	public function unserialize ($content)
	{
		return unserialize($content);
	}

}
