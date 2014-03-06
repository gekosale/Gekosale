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
namespace Gekosale\Core;

use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ControllerResolver
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ControllerResolver extends BaseControllerResolver
{

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var string Action name
     */
    protected $action;

    /**
     * @var string Controller name
     */
    protected $baseController;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Resolves controller name and action from Request
     *
     * @param Request $request
     *
     * @return array|bool|callable|false|mixed
     */
    public function getController(Request $request)
    {
        $this->action         = $request->attributes->get('action');
        $this->baseController = $request->attributes->get('controller');
        $controllerObject     = $this->createController($this->baseController);

        return $controllerObject;
    }

    /**
     * Creates and returns controller instance
     *
     * @param string $class
     *
     * @return array|mixed
     */
    protected function createController($class)
    {
        $controller = new $class();

        if ($controller instanceof ContainerAwareInterface) {
            $controller->setContainer($this->container);
        }

        return array(
            $controller,
            $this->action
        );
    }
}
