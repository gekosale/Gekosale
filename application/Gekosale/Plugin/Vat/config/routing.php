<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Plugin
 * @subpackage  Gekosale\Plugin\Vat
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$this->collection = new RouteCollection();

$this->collection->add('admin.vat.index', new Route('/admin/vat', array(
    'controller' => 'Gekosale\Plugin\Vat\Controller\Admin\Vat',
    'mode' => 'admin',
    'action' => 'index',
    'param' => NULL
)));

$this->collection->add('admin.vat.add', new Route('/admin/vat/add', array(
    'controller' => 'Gekosale\Plugin\Vat\Controller\Admin\Vat',
    'mode' => 'admin',
    'action' => 'add',
    'param' => NULL
)));

$this->collection->add('admin.vat.edit', new Route('/admin/vat/edit/{id}', array(
    'controller' => 'Gekosale\Plugin\Vat\Controller\Admin\Vat',
    'mode' => 'admin',
    'action' => 'edit',
    'id' => NULL
)));

$this->collection->add('admin.vat.delete', new Route('/admin/vat/delete/{id}', array(
    'controller' => 'Gekosale\Plugin\Vat\Controller\Admin\Vat',
    'mode' => 'admin',
    'action' => 'delete',
    'id' => NULL
)));

return $this->collection;
