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

namespace Gekosale\Cache\Storage;
use Exception;

class Memcache extends \Memcache
{
	protected $compression = 0;
	protected $prefix;

	public function __construct (Array $settings)
	{
		$this->host = $settings['host'];

		$this->port = (int) $settings['port'];

		if (isset($settings['zlib_compression']) && $settings['zlib_compression'] == 1){
			if (extension_loaded('zlib')){
				$this->compression = MEMCACHE_COMPRESSED;
			}
			else{
				trigger_error('zlib module not loaded. Compression not set (memcache).', E_USER_WARNING);
			}
		}

		if (! @$this->pconnect($this->host, $this->port)){
			throw new Exception('Can\'t connect to Memcached server. Sorry.');
		}

		$prefix = \Gekosale\App::getConfig('database');
		$this->prefix = ! empty($settings['prefix']) ? $settings['prefix'] : $prefix['dbname'];
	}

	protected function getId ($name)
	{
		if (strncmp('session_', $name, 8) === 0) {
			return $this->prefix . '_' . strtolower($name);
		}

		$cacheid = \Gekosale\Helper::getViewId() . '_' . \Gekosale\Helper::getLanguageId();

		return $this->prefix . '_' . strtolower($name) . '_' . $cacheid;
	}

	public function save ($name, $value, $time)
	{
		return parent::set($this->getId($name), $value, $this->compression, $time);
	}

	public function load ($name)
	{
		return parent::get($this->getId($name));
	}

	public function delete ($name)
	{
		if (strncmp('session_', $name, 8) === 0) {
			parent::delete($this->prefix . '_' . strtolower($name));
		}

		foreach (\Gekosale\Helper::getViewIds() as $viewId) {
			$cacheid = $viewId . '_' . \Gekosale\Helper::getLanguageId();
			parent::delete($this->prefix . '_' . strtolower($name) . '_' . $cacheid);
		}
	}

	public function deleteAll ()
	{
		parent::flush();
	}

}
