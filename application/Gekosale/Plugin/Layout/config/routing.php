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

$layoutCollection = new RouteCollection();

$layoutThemeController = 'Gekosale\Plugin\Layout\Controller\Admin\LayoutThemeController';
$layoutPageController  = 'Gekosale\Plugin\Layout\Controller\Admin\LayoutPageController';
$layoutBoxController   = 'Gekosale\Plugin\Layout\Controller\Admin\LayoutBoxController';

/*
 * LayoutTheme
 */
$layoutThemeCollection = new RouteCollection();

$layoutThemeCollection->add('admin.layout_theme.index', new Route('/index', array(
    '_controller' => $layoutThemeController,
    '_mode'       => 'admin',
    '_action'     => 'indexAction'
)));

$layoutThemeCollection->add('admin.layout_theme.add', new Route('/add', array(
    '_controller' => $layoutThemeController,
    '_mode'       => 'admin',
    '_action'     => 'addAction'
)));

$layoutThemeCollection->add('admin.layout_theme.edit', new Route('/edit/{id}', array(
    '_controller' => $layoutThemeController,
    '_mode'       => 'admin',
    '_action'     => 'editAction',
    'id'          => null
)));

$layoutThemeCollection->addPrefix('/admin/layout_theme');

/*
 * LayoutPage
 */
$layoutPageCollection = new RouteCollection();

$layoutPageCollection->add('admin.layout_page.index', new Route('/index', array(
    '_controller' => $layoutPageController,
    '_mode'       => 'admin',
    '_action'     => 'indexAction'
)));

$layoutPageCollection->add('admin.layout_page.add', new Route('/add', array(
    '_controller' => $layoutPageController,
    '_mode'       => 'admin',
    '_action'     => 'addAction'
)));

$layoutPageCollection->add('admin.layout_page.edit', new Route('/edit/{id},{page}', array(
    '_controller' => $layoutPageController,
    '_mode'       => 'admin',
    '_action'     => 'editAction',
    'id'          => null,
    'page'        => null
)));

$layoutPageCollection->addPrefix('/admin/layout_page');

/*
 * LayoutBox
 */
$layoutBoxCollection = new RouteCollection();

$layoutBoxCollection->add('admin.layout_box.index', new Route('/index', array(
    '_controller' => $layoutBoxController,
    '_mode'       => 'admin',
    '_action'     => 'indexAction'
)));

$layoutBoxCollection->add('admin.layout_box.add', new Route('/add', array(
    '_controller' => $layoutBoxController,
    '_mode'       => 'admin',
    '_action'     => 'addAction'
)));

$layoutBoxCollection->add('admin.layout_box.edit', new Route('/edit/{id}', array(
    '_controller' => $layoutBoxController,
    '_mode'       => 'admin',
    '_action'     => 'editAction',
    'id'          => null
)));

$layoutBoxCollection->addPrefix('/admin/layout_box');

$layoutCollection->addCollection($layoutThemeCollection);
$layoutCollection->addCollection($layoutPageCollection);
$layoutCollection->addCollection($layoutBoxCollection);

return $layoutCollection;
