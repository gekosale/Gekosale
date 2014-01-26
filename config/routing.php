<?php

use Symfony\Component\Routing;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$this->routes = new RouteCollection();

$this->routes->add(
    'dashboard',
    new Route('/',
        array(
             'mode'       => 'frontend',
             'controller' => 'Gekosale\Component\Dashboard\Controller\Frontend\Dashboard',
             'action'     => 'index',
             'param'      => null
        ),
        array(
             '_scheme' => 'http'
        ))
);

$this->routes->add(
    'admin.dashboard',
    new Route('/admin',
        array(
             'mode'       => 'admin',
             'controller' => 'Gekosale\Component\Dashboard\Controller\Admin\Dashboard',
             'action'     => 'index',
             'param'      => null
        ),
        array(
             '_scheme' => 'http'
        ))
);

return $this->routes;