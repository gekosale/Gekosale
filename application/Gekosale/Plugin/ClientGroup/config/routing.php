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

$controller = 'Gekosale\Plugin\ClientGroup\Controller\Admin\ClientGroupController';

$collection->add('admin.client_group.index', new Route('/index', array(
    '_controller' => $controller,
    '_mode'       => 'admin',
    '_action'     => 'indexAction'
)));

$collection->add('admin.client_group.add', new Route('/add', array(
    '_controller' => $controller,
    '_mode'       => 'admin',
    '_action'     => 'addAction'
)));

$collection->add('admin.client_group.edit', new Route('/edit/{id}', array(
    '_controller' => $controller,
    '_mode'       => 'admin',
    '_action'     => 'editAction',
    'id'         => null
)));

$collection->addPrefix('/admin/client_group');

return $collection;
