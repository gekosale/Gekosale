<?php

namespace Gekosale\Core\Resolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Gekosale\Core\Registry;
use Gekosale\Core\App;

class Controller extends BaseControllerResolver
{

    protected $container;

    protected $mode;

    protected $action;

    protected $namespaces;

    protected $baseController;

    public function __construct (ContainerInterface $container = NULL)
    {
        $this->container = $container;
    }

    public function getController (Request $request)
    {
        $this->mode = ucfirst($request->attributes->get('mode'));
        $this->action = ucfirst($request->attributes->get('action'));
        $this->baseController = $request->attributes->get('controller');
        $this->namespaces = App::getRegistry()->loader->getNamespaces();
        $this->classesMap = $this->container->get('classmapper')->getClassMap();
        
        $lastNs = '';
        foreach ($this->namespaces as $namespace){
            $ns = $namespace . DS . $this->mode . DS . strtolower($this->baseController . DS . 'controller' . DS . $this->baseController);
            if (isset($this->classesMap[$ns])){
                require_once $this->classesMap[$ns];
                $lastNs = $namespace;
            }
        }
        
        if (! empty($lastNs)){
            $class = $lastNs . '\\' . $this->baseController . 'Controller';
        }
        
        $controllerObject = $this->createController($class);
        
        return $controllerObject;
    }

    protected function createController ($class)
    {
        $controllerObject = new $class(App::getRegistry(), $this->container);
        $controllerObject->setDesignPath(strtolower($this->baseController . DS . $this->action . DS));
        
        return array(
            $controllerObject,
            $this->action
        );
    }
}
