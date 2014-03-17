<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$collection = new RouteCollection();

$controller = 'Gekosale\Plugin\CacheManager\Controller\Admin\CacheManagerController';

$collection->add('admin.cache_manager.index', new Route('/index', array(
    '_controller' => $controller,
    '_mode'       => 'admin',
    '_action'     => 'indexAction'
)));

$collection->add('admin.cache_manager.delete', new Route('/delete', array(
    '_controller' => $controller,
    '_mode'       => 'admin',
    '_action'     => 'deleteAction'
)));

$collection->addPrefix('/admin/cache_manager');

return $collection;
