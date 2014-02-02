<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @category    Gekosale 
 * @package     Gekosale\Component\Cache
 * @subpackage  Gekosale\Component\Cache\Storage
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Cache\Storage;

class Memcache extends \Memcache
{

    protected $compression = 0;

    protected $prefix;

    public function __construct (Array $settings)
    {
        $this->host = $settings['host'];
        
        $this->port = (int) $settings['port'];
        
        if (isset($settings['zlib_compression']) && $settings['zlib_compression'] == 1) {
            if (extension_loaded('zlib')) {
                $this->compression = MEMCACHE_COMPRESSED;
            }
            else {
                trigger_error('zlib module not loaded. Compression not set (memcache).', E_USER_WARNING);
            }
        }
        
        if (! @$this->pconnect($this->host, $this->port)) {
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
