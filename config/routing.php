<?php

use Symfony\Component\Routing;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$this->routes = new RouteCollection();

$this->routes->add('frontend.home', new Route('/', array(
    'mode' => 'frontend',
    'controller' => 'Gekosale\Component\Dashboard\Controller\Frontend\Dashboard',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http'
)));

return $this->routes;