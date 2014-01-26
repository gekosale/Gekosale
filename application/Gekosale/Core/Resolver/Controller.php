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
        $this->action = $request->attributes->get('action');
        $this->baseController = $request->attributes->get('controller');
        $controllerObject = $this->createController($this->baseController);
        return $controllerObject;
    }

    protected function createController ($class)
    {
        $controller = new $class();
        
        if ($controller instanceof ContainerAwareInterface){
            $controller->setContainer($this->container);
        }
        
        return array(
            $controller,
            $this->action
        );
    }
}
