<?php

namespace Gekosale;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Matcher\TraceableUrlMatcher;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegExIterator;
use FilesystemIterator;

class Router
{

    const FRONTEND_PANE = 'Frontend';

    const ADMIN_PANE = 'Admin';

    const SUPER_PANE = 'Super';

    protected $registry;

    protected $container;

    protected $path;

    public $modelFile;

    protected $model;

    protected $action = 'index';

    protected $param = Array();

    protected $parsedURL;

    protected $adminitrativeMode = 0;

    protected $_adminPane = '';

    protected $exceptionModel;

    protected $baseController;

    protected $baseControllerFullName;

    protected $context;

    protected $route;

    protected $routes;

    protected $request;

    protected $matcher;

    protected $generator;

    protected $classesMap = FALSE;

    protected $is404 = FALSE;

    public function __construct ($registry, $container)
    {
        $this->registry = $registry;
        
        $this->container = $container;
        
        $this->path = ROOTPATH . 'plugin';
        
        $this->route = $this->container->get('router')->getMatcher()->match(App::getRequest()->getPathInfo());
        
        $this->baseController = $this->route['controller'];
        
        $this->baseControllerFullName = 'Gekosale\\' . ucfirst($this->baseController) . 'Controller';
        
        $this->action = $this->route['action'];
        
        $this->param = $this->route['param'];
        
        $this->setAdministrativeMode($this->route['mode'] == 'admin' ? 1 : 0);
        
        $this->context = new RequestContext();
        
        $this->context->fromRequest(App::getRequest());
        
        $this->container->get('router')->setContext($this->context);
        
        $this->classesMap = $this->container->get('classmapper')->getClassMap();
    }

    public function getPath ()
    {
        return $this->path;
    }

    public function getBaseController ()
    {
        return $this->baseController;
    }

    public function getUri ()
    {
        return $this->request->getUri();
    }

    public function generate ($route, $absolute = false, $params = Array())
    {
        return $this->getGenerator()->generate($route, $params, $absolute);
    }

    public function getParamFromRoute ($param, $defaultValue)
    {
        return isset($this->route[$param]) ? $this->route[$param] : $defaultValue;
    }

    public function getGenerator ()
    {
        return $this->container->get('router')->getGenerator();
    }

    public function getAdministrativeMode ()
    {
        return $this->adminitrativeMode;
    }

    protected function setAdministrativeMode ($value = 0)
    {
        $this->adminitrativeMode = (0 == $value || NULL == App::getContainer()->get('session')->getActiveUserid()) ? 0 : 1;
    }

    public function getModeName ()
    {
        return ($this->adminitrativeMode == 1) ? self::ADMIN_PANE : self::FRONTEND_PANE;
    }

    public function getMode ()
    {
        return $this->adminitrativeMode;
    }

    public function getAdminPaneName ()
    {
        return __ADMINPANE__ . '/';
    }

    public function getParams ()
    {
        return $this->param;
    }

    public function getCurrentController ()
    {
        return (isset($this->baseController) && $this->baseController != '') ? $this->baseController : NULL;
    }

    public function getAction ()
    {
        return (isset($this->action) && $this->action != '') ? $this->action : 'index';
    }

    public function is404 ()
    {
        return $this->is404;
    }

    public function url ($route, $controller, $action = 'index', $param = null)
    {
        return $this->container->get('router')->getGenerator()->generate($route, array(
            'controller' => $controller,
            'action' => $action,
            'param' => $param
        ));
    }
}
