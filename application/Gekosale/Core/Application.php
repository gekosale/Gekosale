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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class Application
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Application
{

    /**
     * Container instance
     *
     * @var object
     */
    protected $container;

    /**
     * Request instance
     *
     * @var object
     */
    protected $request;

    /**
     * Response instance
     *
     * @var object
     */
    protected $response;

    /**
     * Stopwatch component instance
     *
     * @var object
     */
    protected $stopwatch;

    /**
     * True if the debug mode is enabled, false otherwise
     *
     * @var bool
     */
    protected $isDebug;

    /**
     * Constructor
     *
     * @param bool $isDebug Enable or disable debug mode in application
     */
    public function __construct($isDebug)
    {
        $this->isDebug   = (bool)$isDebug;
        $this->stopwatch = new Stopwatch();

        $this->stopwatch->start('application');

        // Create request instance
        $this->request = Request::createFromGlobals();

        // Check if service container exists and/or needs to be regenerated
        $serviceContainerBuilder = new ServiceContainerBuilder($this->getKernelParameters(), $this->isDebug);
        $serviceContainerBuilder->check();

        // Init Service Container
        $this->container = new ServiceContainer();
    }

    /**
     * Resolves controller and dispatch the application
     *
     * @return  void
     */
    public function run()
    {
        $this->response = $this->container->get('kernel')->handle($this->request);
        $this->response->send();
    }

    /**
     * Stops application and triggers termination events
     *
     * @return  void
     */
    public function stop()
    {
        $this->container->get('kernel')->terminate($this->request, $this->response);
        $event = $this->stopwatch->stop('application');
//        echo $event->getDuration();
    }

    /**
     * Returns all globally accessible kernel parameters
     *
     * @return  array
     */
    protected function getKernelParameters()
    {
        return [
            'application.root_path'  => ROOTPATH,
            'application.debug_mode' => $this->isDebug
        ];
    }
}