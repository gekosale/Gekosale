<?php

/**
 * Gekosale, Open Source E-Commerce Solution 
 * 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed with this source code. 
 * 
 * @category    Gekosale 
 * @package     Gekosale\Core 
 * @subpackage  Gekosale\Core\Resolver
 * @author      Adam Piotrowski <adam@gekosale.com>
 * @copyright   Copyright (c) 2008-2014 Gekosale sp. z o.o. (http://www.gekosale.com)
 */
namespace Gekosale\Core\Resolver;

use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Annotations\AnnotationReader;

class Controller extends BaseControllerResolver
{

    protected $container;

    protected $mode;

    protected $action;

    protected $namespaces;

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
        $annotationReader = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($class);
        $reflectionMethod = new \ReflectionMethod($class, $this->action);
        $classAnnotations = $annotationReader->getClassAnnotations($reflectionClass);
        $methodAnnotations = $annotationReader->getMethodAnnotations($reflectionMethod);
        
        if ($controller instanceof ContainerAwareInterface) {
            $controller->setContainer($this->container);
        }
        
        foreach ($methodAnnotations as $annotation) {
            if ($annotation instanceof \Gekosale\Core\Model) {
                $controller->setModel($annotation->model, $annotation->alias);
            }
            if ($annotation instanceof \Gekosale\Core\Form) {
                $controller->setForm($annotation->form, $annotation->alias);
            }
        }
        
        return array(
            $controller,
            $this->action
        );
    }
}
