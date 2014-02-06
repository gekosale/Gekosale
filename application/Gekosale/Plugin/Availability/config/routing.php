<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Plugin
 * @subpackage  Gekosale\Plugin\Availability
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$this->collection = new RouteCollection();

$this->collection->add('admin.availability.index', new Route('/admin/availability', array(
    'controller' => 'Gekosale\Plugin\Availability\Controller\Admin\Availability',
    'mode' => 'admin',
    'action' => 'index',
    'param' => NULL
)));

$this->collection->add('admin.availability.add', new Route('/admin/availability/add', array(
    'controller' => 'Gekosale\Plugin\Availability\Controller\Admin\Availability',
    'mode' => 'admin',
    'action' => 'add',
    'param' => NULL
)));

$this->collection->add('admin.availability.edit', new Route('/admin/availability/edit/{id}', array(
    'controller' => 'Gekosale\Plugin\Availability\Controller\Admin\Availability',
    'mode' => 'admin',
    'action' => 'edit',
    'id' => NULL
)));

$this->collection->add('admin.availability.delete', new Route('/admin/availability/delete/{id}', array(
    'controller' => 'Gekosale\Plugin\Availability\Controller\Admin\Availability',
    'mode' => 'admin',
    'action' => 'delete',
    'id' => NULL
)));

return $this->collection;
