<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * @category    Gekosale
 * @package     Gekosale\Plugin
 * @subpackage  Gekosale\Plugin\Company
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$this->collection = new RouteCollection();

$this->collection->add('admin.company.index', new Route('/admin/company', array(
    'controller' => 'Gekosale\Plugin\Company\Controller\Admin\Company',
    'mode' => 'admin',
    'action' => 'index',
    'param' => NULL
)));

$this->collection->add('admin.company.add', new Route('/admin/company/add', array(
    'controller' => 'Gekosale\Plugin\Company\Controller\Admin\Company',
    'mode' => 'admin',
    'action' => 'add',
    'param' => NULL
)));

$this->collection->add('admin.company.edit', new Route('/admin/company/edit/{id}', array(
    'controller' => 'Gekosale\Plugin\Company\Controller\Admin\Company',
    'mode' => 'admin',
    'action' => 'edit',
    'id' => NULL
)));

$this->collection->add('admin.company.delete', new Route('/admin/company/delete/{id}', array(
    'controller' => 'Gekosale\Plugin\Company\Controller\Admin\Company',
    'mode' => 'admin',
    'action' => 'delete',
    'id' => NULL
)));

return $this->collection;
