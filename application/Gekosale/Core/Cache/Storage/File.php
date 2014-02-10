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

use Symfony\Component\DependencyInjection\ContainerInterface;

class File
{

    protected $path;

    protected $cacheid;

    protected $extension;

    protected $container;

    public function __construct (ContainerInterface $container, $path, $extension)
    {
        $this->container = $container;
        $this->path = $path;
        $this->extension = $extension;
        $this->cacheid = NULL;
    }

    public function save ($name, $value)
    {
        $this->container->get('filesystem')->dumpFile($this->getCacheFileName($name), $value);
    }

    public function load ($name)
    {
        if (($content = @file_get_contents($this->getCacheFileName($name))) === FALSE) {
            return FALSE;
        }
        
        clearstatcache();
        if (filemtime($this->getCacheFileName($name)) < time()) {
            return FALSE;
        }
        
        return $content;
    }

    public function delete ($name)
    {
        foreach (glob($this->path . strtolower($name) . '*') as $key => $fn) {
            @unlink($fn);
        }
    }

    public function deleteAll ()
    {
        foreach (glob($this->path . '*' . $this->extension) as $fn) {
            @unlink($fn);
        }
    }

    public function getCacheFileName ($name)
    {
        $cacheid = $this->container->get('helper')->getViewId() . '_' . $this->container->get('helper')->getLanguageId();
        
        return $this->path . strtolower($name) . '_' . $cacheid . $this->extension;
    }
}
