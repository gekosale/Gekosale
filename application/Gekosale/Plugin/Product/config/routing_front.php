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

$controller = 'Gekosale\Plugin\Product\Controller\Frontend\ProductController';

$collection->add('frontend.product.index', new Route('/product/{slug}', [
    '_controller' => $controller,
    '_mode'       => 'frontend',
    '_action'     => 'indexAction',
    'slug'        => null
], [
    'slug' => '.+'
]));

return $collection;
