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
namespace Gekosale\Core\Resolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Controller extends ControllerResolver
{

    protected $container;

    protected $action;

    protected $baseController;

    public function __construct (ContainerInterface $container = null)
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
