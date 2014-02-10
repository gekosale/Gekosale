<?php

/**
 * Gekosale Open-Source E-Commerce Platform
 * 
 * This file is part of the Gekosale package.
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$this->collection = new RouteCollection();

$this->collection->add('admin.currency.index', new Route('/admin/currency', array(
    'controller' => 'Gekosale\Plugin\Currency\Controller\Admin\CurrencyController',
    'mode' => 'admin',
    'action' => 'index',
)));

$this->collection->add('admin.currency.add', new Route('/admin/currency/add', array(
    'controller' => 'Gekosale\Plugin\Currency\Controller\Admin\CurrencyController',
    'mode' => 'admin',
    'action' => 'add',
)));

$this->collection->add('admin.currency.edit', new Route('/admin/currency/edit/{id}', array(
    'controller' => 'Gekosale\Plugin\Currency\Controller\Admin\CurrencyController',
    'mode' => 'admin',
    'action' => 'edit',
    'id' => NULL
)));

return $this->collection;
