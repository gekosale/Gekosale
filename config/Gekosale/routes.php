<?php

namespace Gekosale;
use Symfony\Component\Routing;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Gekosale\Core\Seo;

$this->routes = new RouteCollection();

$this->routes->add('frontend.home', new Route('/', array(
    'mode' => 'frontend',
    'controller' => 'mainside',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http'
)));

$this->routes->add('frontend.error', new Route('/error', array(
    'mode' => 'frontend',
    'controller' => 'error',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http'
)));

$this->routes->add('frontend.exchange', new Route('/exchange/{param}', array(
    'mode' => 'frontend',
    'controller' => 'exchange',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http'
)));

$this->routes->add('frontend.webapi', new Route('/webapi/{module}/{method}/{param}', array(
    'mode' => 'frontend',
    'controller' => 'webapi',
    'action' => 'index',
    'module' => NULL,
    'method' => NULL,
    'param' => NULL
), array(
    '_scheme' => 'http'
)));

$this->routes->add('frontend.redirect', new Route('/redirect/{action}/{param}', array(
    'mode' => 'frontend',
    'controller' => 'redirect',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http'
)));

$this->routes->add('frontend.mainside', new Route('/' . Seo::getSeo('mainside'), array(
    'mode' => 'frontend',
    'controller' => 'mainside',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http'
)));

$this->routes->add('frontend.newsletter', new Route('/' . Seo::getSeo('newsletter') . '/{action}/{param}', array(
    'mode' => 'frontend',
    'controller' => 'newsletter',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => SSLNAME
)));

$this->routes->add('frontend.login', new Route('/login/{param}', array(
    'mode' => 'frontend',
    'controller' => 'login',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http'
)));

$this->routes->add('frontend.forgotlogin', new Route('/forgotlogin', array(
    'mode' => 'frontend',
    'controller' => 'forgotlogin',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http'
)));

$this->routes->add('frontend.logout', new Route('/logout', array(
    'mode' => 'frontend',
    'controller' => 'logout',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http'
)));

$this->routes->add('frontend.cart', new Route('/' . Seo::getSeo('cart'), array(
    'mode' => 'frontend',
    'controller' => 'cart',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => SSLNAME
)));

$this->routes->add('frontend.checkout', new Route('/' . Seo::getSeo('checkout'), array(
    'mode' => 'frontend',
    'controller' => 'checkout',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => SSLNAME
)));

$this->routes->add('frontend.conditions', new Route('/' . Seo::getSeo('conditions'), array(
    'mode' => 'frontend',
    'controller' => 'conditions',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http'
)));

$this->routes->add('frontend.finalization', new Route('/' . Seo::getSeo('finalization'), array(
    'mode' => 'frontend',
    'controller' => 'finalization',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => SSLNAME
)));

$this->routes->add('frontend.clientsettings', new Route('/' . Seo::getSeo('clientsettings'), array(
    'mode' => 'frontend',
    'controller' => 'clientsettings',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => SSLNAME
)));

$this->routes->add('frontend.clientorder', new Route('/' . Seo::getSeo('clientorder') . '/{param}', array(
    'mode' => 'frontend',
    'controller' => 'clientorder',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => SSLNAME
)));

$this->routes->add('frontend.integration', new Route('/' . Seo::getSeo('integration') . '/{param}', array(
    'mode' => 'frontend',
    'controller' => 'integration',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http',
    'param' => '(ceneo|domodi|ceneria|cenuj|kreocen|kupujemy|najtaniej24|nokaut|oferciak|okazje|radar|skapiec|smartbay|szoker|tortura|webkupiec)(\.xml)?'
)));

$this->routes->add('frontend.firmes', new Route('/firmes/{param}', array(
    'mode' => 'frontend',
    'controller' => 'firmes',
    'action' => 'index'
), array(
    '_scheme' => 'http',
    'param' => 'subiekt|optima|navireo|wfmag|hermes'
)));

$this->routes->add('frontend.clientaddress', new Route('/' . Seo::getSeo('clientaddress'), array(
    'mode' => 'frontend',
    'controller' => 'clientaddress',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => SSLNAME
)));

$this->routes->add('frontend.invoice', new Route('/' . Seo::getSeo('invoice') . '/{param}', array(
    'mode' => 'frontend',
    'controller' => 'invoice',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => SSLNAME
)));

$this->routes->add('frontend.searchresults', new Route('/searchresults/{param}', array(
    'mode' => 'frontend',
    'controller' => 'searchresults',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => SSLNAME
)));

$this->routes->add('frontend.productcart', new Route('/' . Seo::getSeo('productcart') . '/{param}', array(
    'mode' => 'frontend',
    'controller' => 'productcart',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http',
    'param' => '.+'
)));

$this->routes->add('frontend.producerlist', new Route('/' . Seo::getSeo('producerlist') . '/{param},{collection},{currentPage},{orderBy},{orderDir},{viewType},{priceFrom},{priceTo},{producers},{attributes}', array(
    'mode' => 'frontend',
    'controller' => 'producerlist',
    'action' => 'index',
    'param' => NULL,
    'collection' => NULL,
    'orderBy' => NULL,
    'orderDir' => NULL,
    'viewType' => NULL,
    'currentPage' => NULL,
    'priceFrom' => NULL,
    'priceTo' => NULL,
    'producers' => NULL,
    'attributes' => NULL
), array(
    '_scheme' => 'http',
    'param' => '[^,]+',
    'collection' => '[^,]+',
    'orderBy' => '\w+',
    'orderDir' => 'asc|desc',
    'currentPage' => '\d{1,10}',
    'priceFrom' => '[\d+\.]+',
    'priceTo' => '[\d+\.]+',
    'producers' => '[\d+_]+',
    'attributes' => '[\d+_]+'
)));

$this->routes->add('frontend.categorylist', new Route('/' . Seo::getSeo('categorylist') . '/{param},{currentPage},{orderBy},{orderDir},{viewType},{priceFrom},{priceTo},{producers},{attributes},{technicaldata}', array(
    'mode' => 'frontend',
    'controller' => 'categorylist',
    'action' => 'index',
    'param' => NULL,
    'orderBy' => NULL,
    'orderDir' => NULL,
    'viewType' => NULL,
    'currentPage' => NULL,
    'priceFrom' => NULL,
    'priceTo' => NULL,
    'producers' => NULL,
    'attributes' => NULL,
    'technicaldata' => NULL
), array(
    '_scheme' => 'http',
    'param' => '[^,]+',
    'currentPage' => '\d{1,10}',
    'orderBy' => '\w+',
    'orderDir' => 'asc|desc',
    'priceFrom' => '[\d+\.]+',
    'priceTo' => '[\d+\.]+',
    'producers' => '[\d_]+',
    'attributes' => '[\d_]+',
    'technicaldata' => '[\d_]+'
)));

$this->routes->add('frontend.staticcontent', new Route('/' . Seo::getSeo('staticcontent') . '/{param}/{slug}', array(
    'mode' => 'frontend',
    'controller' => 'staticcontent',
    'action' => 'index',
    'param' => NULL,
    'slug' => NULL
), array(
    '_scheme' => 'http',
    'param' => '.+',
    'slug' => '[^,]+'
)));

$this->routes->add('frontend.productprint', new Route('/' . Seo::getSeo('productprint') . '/{param}', array(
    'mode' => 'frontend',
    'controller' => 'productprint',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http',
    'param' => '.+'
)));

$this->routes->add('frontend.clientlogin', new Route('/' . Seo::getSeo('clientlogin') . '/{param}', array(
    'mode' => 'frontend',
    'controller' => 'clientlogin',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => SSLNAME
)));

$this->routes->add('frontend.registration', new Route('/' . Seo::getSeo('registration') . '/{param}', array(
    'mode' => 'frontend',
    'controller' => 'registration',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => SSLNAME
)));

$this->routes->add('frontend.contact', new Route('/' . Seo::getSeo('contact') . '/{param}', array(
    'mode' => 'frontend',
    'controller' => 'contact',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => SSLNAME
)));

$this->routes->add('frontend.sitemap', new Route('/' . Seo::getSeo('sitemap') . '/{param}', array(
    'mode' => 'frontend',
    'controller' => 'sitemap',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http',
    'param' => '\d+'
)));

$this->routes->add('frontend.forgotpassword', new Route('/' . Seo::getSeo('forgotpassword') . '/{action}/{param}', array(
    'mode' => 'frontend',
    'controller' => 'forgotpassword',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => SSLNAME
)));

$this->routes->add('frontend.productsearch', new Route('/' . Seo::getSeo('productsearch') . '/{action}/{param},{currentPage},{orderBy},{orderDir},{viewType},{priceFrom},{priceTo},{producers},{attributes},{technicaldata}', array(
    'mode' => 'frontend',
    'controller' => 'productsearch',
    'action' => NULL,
    'param' => NULL,
    'orderBy' => NULL,
    'orderDir' => NULL,
    'viewType' => NULL,
    'currentPage' => NULL,
    'priceFrom' => NULL,
    'priceTo' => NULL,
    'producers' => NULL,
    'attributes' => NULL,
    'technicaldata' => NULL
), array(
    '_scheme' => 'http',
    'param' => '[^,]+',
    'currentPage' => '\d{1,10}',
    'orderBy' => '[\w-\/]+',
    'orderDir' => 'asc|desc',
    'priceFrom' => '[\d+\.]+',
    'priceTo' => '[\d+\.]+',
    'producers' => '[\d+_]+',
    'attributes' => '[\d+_]+',
    'technicaldata' => '[\d+_]+'
)));

$this->routes->add('frontend.productnews', new Route('/' . Seo::getSeo('productnews') . '/{currentPage}', array(
    'mode' => 'frontend',
    'controller' => 'productnews',
    'action' => 'index',
    'currentPage' => NULL,
    'param' => NULL
), array(
    '_scheme' => 'http',
    'currentPage' => '\d{1,10}'
)));

$this->routes->add('frontend.productpromotion', new Route('/' . Seo::getSeo('productpromotion') . '/{currentPage}', array(
    'mode' => 'frontend',
    'controller' => 'productpromotion',
    'action' => 'index',
    'currentPage' => NULL,
    'param' => NULL
), array(
    '_scheme' => 'http',
    'currentPage' => '\d{1,10}'
)));

$this->routes->add('frontend.news', new Route('/' . Seo::getSeo('news') . '/{param}/{slug}', array(
    'mode' => 'frontend',
    'controller' => 'news',
    'action' => 'index',
    'param' => NULL,
    'slug' => NULL
), array(
    '_scheme' => 'http',
    'param' => '.+'
)));

$this->routes->add('frontend.kodyrabatowe', new Route('/kodyrabatowe', array(
    'mode' => 'frontend',
    'controller' => 'kodyrabatowe',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http',
    'param' => '.+'
)));

$this->routes->add('frontend.payment', new Route('/' . Seo::getSeo('payment') . '/{action}/{param}', array(
    'mode' => 'frontend',
    'controller' => 'payment',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => SSLNAME,
    'param' => '.+'
)));

$this->routes->add('frontend.instancereport', new Route('/instancereport/{action}/{param}', array(
    'mode' => 'frontend',
    'controller' => 'instancereport',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http',
    'param' => '.+'
)));

$this->routes->add('admin.login', new Route('/' . __ADMINPANE__, array(
    'mode' => 'admin',
    'controller' => 'login',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http'
)));

$this->routes->add('admin', new Route('/' . __ADMINPANE__ . '/{controller}/{action}/{param}', array(
    'mode' => 'admin',
    'action' => 'index',
    'param' => NULL
), array(
    '_scheme' => 'http'
)));

$this->routes->add('frontend.wishlist', new Route('/' . Seo::getSeo('wishlist') . '/{currentPage}', array(
    'mode' => 'frontend',
    'controller' => 'wishlist',
    'action' => 'index',
    'param' => NULL,
    'currentPage' => NULL
), array(
    'currentPage' => '\d{1,10}'
)));

$this->routes->add('frontend.productcompare', new Route('/' . Seo::getSeo('productcompare'), array(
    'mode' => 'frontend',
    'controller' => 'productcompare',
    'action' => 'index',
    'param' => NULL
)));

return $this->routes;