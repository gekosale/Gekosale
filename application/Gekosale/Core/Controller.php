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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Controller
 *
 * Provides common methods needed in controllers
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class Controller extends Component
{

    protected $parameters;

    /**
     * Generates relative or absolute url based on given route and parameters
     * 
     * @param string $route
     * @param array  $parameters
     * @param string $referenceType
     * 
     * @return string Generated url
     */
    public function generateUrl ($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * Redirects user to a given url
     * 
     * @param string $url
     * @param number $status
     * @return RedirectResponse
     */
    public function redirect ($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }
}