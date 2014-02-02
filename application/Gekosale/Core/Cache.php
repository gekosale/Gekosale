<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Core
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Cache;

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
