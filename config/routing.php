<?php
/*
 * Gekosale, Open Source E-Commerce Solution
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * @category    Gekosale
 * @package     Gekosale\Plugin\Vat
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$this->collection = new RouteCollection();

$this->collection->add('admin.dashboard', new Route('/admin/dashboard', array(
    'controller' => 'Gekosale\Plugin\Dashboard\Controller\Admin\Dashboard',
    'mode' => 'admin',
    'action' => 'index',
    'param' => NULL
)));

return $this->collection;